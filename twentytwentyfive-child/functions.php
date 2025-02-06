<?php 


/* Twentytwentyfive Child theme */
if ( ! function_exists( 'recipe_theme_enqueue_styles' ) ) {
    add_action( 'wp_enqueue_scripts', 'recipe_theme_enqueue_styles' );
    
    function recipe_theme_enqueue_styles() {
        // Parent theme stylesheet
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

        // Child theme stylesheet
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ) );

        // Enqueue Tailwind CSS
        wp_enqueue_style( 'tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css' );

        // Enqueue Bootstrap CSS
        wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2' );

        wp_enqueue_style( 'animate-css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css' );


        // Enqueue Bootstrap JS
        wp_enqueue_script( 'bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), '4.5.2', true );

        // Enqueue Tailwind JS (Optional, only needed if you are using Tailwind's JS features like animations)
        wp_enqueue_script( 'tailwind-js', 'https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.js', array(), '3.0.0', true );
    }
}


function create_recipe_post_type() {
    $labels = array(
        'name'               => 'Recipes',
        'singular_name'      => 'Recipe',
        'menu_name'          => 'Recipes',
        'all_items'          => 'All Recipes',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Recipe',
        'edit_item'          => 'Edit Recipe',
        'new_item'           => 'New Recipe',
        'view_item'          => 'View Recipe',
        'search_items'       => 'Search Recipes',
        'not_found'          => 'No Recipes found',
        'not_found_in_trash' => 'No Recipes found in Trash'
    );
    
    $args = array(
        'label'              => 'recipe',
        'description'        => 'Recipe blog section',
        'labels'             => $labels,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-carrot',
        'rewrite'            => array('slug' => 'recipes'),
        'capability_type'    => 'post',
        'has_archive'        => true
    );
    
    register_post_type('recipe', $args);
}
add_action('init', 'create_recipe_post_type');

// Register Custom Taxonomy for Recipe Categories
function create_recipe_taxonomy() {
    $labels = array(
        'name'              => 'Recipe Categories',
        'singular_name'     => 'Recipe Category',
        'search_items'      => 'Search Recipe Categories',
        'all_items'         => 'All Recipe Categories',
        'edit_item'         => 'Edit Recipe Category',
        'update_item'       => 'Update Recipe Category',
        'add_new_item'      => 'Add New Recipe Category',
        'new_item_name'     => 'New Recipe Category Name',
        'menu_name'         => 'Recipe Categories'
    );
    
    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'recipe-category')
    );
    
    register_taxonomy('recipe_category', array('recipe'), $args);
}
add_action('init', 'create_recipe_taxonomy');

// Add Custom Metabox for Ingredients & Preparation Steps
function add_recipe_metaboxes() {
    add_meta_box(
        'recipe_ingredients',
        'Ingredients List',
        'render_recipe_ingredients_metabox',
        'recipe',
        'normal',
        'high'
    );
    
    add_meta_box(
        'recipe_steps',
        'Preparation Steps',
        'render_recipe_steps_metabox',
        'recipe',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_recipe_metaboxes');

// Render Ingredients Metabox
// Render Ingredients Metabox
function render_recipe_ingredients_metabox($post) {
    $ingredients = get_post_meta($post->ID, 'recipe_ingredients', true);
    $ingredients = is_array($ingredients) ? $ingredients : []; // Ensure it's an array

    echo '<div style="padding:10px;" id="ingredients-repeater">';
    foreach ($ingredients as $ingredient) {
        echo '<div style = padding:10px; margin:5px;><input type="text" name="recipe_ingredients[]" value="' . esc_attr($ingredient) . '" placeholder="Enter ingredient" style="width: 80%;" />';
        echo '<button type="button" class="remove-field">&times;</button></div>';
    }
    echo '</div>';
    echo '<button type="button" id="add-ingredient">Add Ingredient</button>';
}

// Render Preparation Steps Metabox
function render_recipe_steps_metabox($post) {
    $steps = get_post_meta($post->ID, 'recipe_steps', true);
    $steps = is_array($steps) ? $steps : []; // Ensure it's an array

    echo '<div style="padding:10px;" id="steps-repeater">';
    foreach ($steps as $step) {
        echo '<div style="padding:10px;"><input type="text" name="recipe_steps[]" value="' . esc_attr($step) . '" placeholder="Enter step" style="width: 80%;" />';
        echo '<button type="button" class="remove-field">&times;</button></div>';
    }
    echo '</div>';
    echo '<button type="button" id="add-step">Add Step</button>';
}


// Save Metabox Data
function save_recipe_metaboxes($post_id) {
    if (isset($_POST['recipe_ingredients'])) {
        update_post_meta($post_id, 'recipe_ingredients', array_map('sanitize_text_field', $_POST['recipe_ingredients']));
    }
    if (isset($_POST['recipe_steps'])) {
        update_post_meta($post_id, 'recipe_steps', array_map('sanitize_text_field', $_POST['recipe_steps']));
    }
}
add_action('save_post', 'save_recipe_metaboxes');

// Add JavaScript for Repeater Fields
add_action('admin_footer', function () {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        function addField(containerId, inputName, buttonId) {
            let container = document.getElementById(containerId);
            let button = document.getElementById(buttonId);
            button.addEventListener("click", function() {
                let div = document.createElement("div");
                div.innerHTML = `<input type="text" name="${inputName}[]" placeholder="Enter value" style="width: 80%;" /> <button type="button" class="remove-field">&times;</button>`;
                container.appendChild(div);
            });
            container.addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-field")) {
                    event.target.parentElement.remove();
                }
            });
        }
        addField("ingredients-repeater", "recipe_ingredients", "add-ingredient");
        addField("steps-repeater", "recipe_steps", "add-step");
    });
    </script>';
});




    
function display_recipes_on_front_page($content) {
    // Check if we are on the homepage
    if (is_front_page()) {
        ob_start(); // Start output buffering
        ?>
        <div class="container">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex flex-col md:flex-row gap-8">
                        
                        <!-- Sidebar: Recipe Categories -->
                        <aside class="w-full md:w-1/4 bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Recipe Categories</h3>
                            <ul class="space-y-2">
                                <?php
                                $categories = get_terms(['taxonomy' => 'recipe_category', 'hide_empty' => false]);
                                
                                if (!is_wp_error($categories) && !empty($categories)) {
                                    foreach ($categories as $category) {
                                        echo '<li><a href="' . esc_url(get_term_link($category)) . '" class="block text-blue-600 hover:text-blue-800 font-medium">' . esc_html($category->name) . '</a></li>';
                                    }
                                } else {
                                    echo '<li class="text-gray-500">No categories found.</li>';
                                }
                                ?>
                            </ul>
                        </aside>

                        <!-- Recipe Listing Grid -->
                        <section class="w-full md:w-3/4">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Latest Recipes</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php
                                $recipe_query = new WP_Query([
                                    'post_type'      => 'recipe',
                                    'posts_per_page' => 9, 
                                    'post_status'    => 'publish',
                                ]);

                                if ($recipe_query->have_posts()) :
                                    while ($recipe_query->have_posts()) : $recipe_query->the_post(); ?>
                                        <div class="border border-gray-200 p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                                            <a href="<?php the_permalink(); ?>" class="block">
                                                <?php if (has_post_thumbnail()) { 
                                                    the_post_thumbnail('medium', ['class' => 'w-full h-48 object-cover rounded-md mb-4']);
                                                } ?>
                                                <h3 class="text-lg font-semibold text-black-800"><?php the_title(); ?></h3>
                                            </a>
                                        </div>
                                    <?php endwhile;
                                    wp_reset_postdata();
                                else :
                                    echo '<p class="col-span-full text-center text-gray-500">No recipes found.</p>';
                                endif;
                                ?>
                            </div>
                        </section>

                    </div>
                </div>
            </div>

        </div>
        <?php

        $custom_content = ob_get_clean(); 

        return $custom_content . $content; // Append custom content to the existing content
    }

    return $content; // Return content unchanged for other pages
}

add_filter('the_content', 'display_recipes_on_front_page');
