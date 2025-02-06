<?php get_header(); ?>

<div class="container">

    <div class="recipe-single max-w-4xl mx-auto p-6 animate__animated animate__fadeIn">
        <h1 class="text-4xl font-bold text-center text-gray-900 mb-6 pb-4 transition-all duration-500 ease-in-out transform hover:scale-105">
            <?php the_title(); ?>
        </h1>

        <?php if (has_post_thumbnail()) { ?>
            <div class="mb-8 flex justify-center">
                <?php the_post_thumbnail('large', ['class' => 'w-full h-auto rounded-lg shadow-xl transform transition duration-500 hover:scale-105']); ?>
            </div>
           
            <div class="bg-opacity-75 mx-auto mt-5 rounded-lg">
                <?php the_content(); 

                $categories = get_the_terms(get_the_ID(), 'recipe_category');  // Fixed taxonomy name here
                if ($categories && !is_wp_error($categories)) {
                    echo '<p class="text-xl font-bold text-gray-800 mb-4"><b>Categories: </b>';
                    $category_list = [];
                    foreach ($categories as $category) {
                        $category_list[] = esc_html($category->name);
                    }
                    echo implode(', ', $category_list); 
                    echo '</p>';
                }
                ?>

            </div>
        
        <?php } ?>

        <div class="ingredients mb-12 bg-gradient-to-r from-red-200 via-yellow-200 to-green-200 p-6 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-xl">
            <h2 class="text-3xl font-semibold text-gray-800 mb-4 animate__animated animate__fadeIn">Ingredients</h2>
            <ul class="list-disc pl-5 space-y-3 text-gray-700">
                <?php
                $ingredients = get_post_meta(get_the_ID(), 'recipe_ingredients', true);
                if (!empty($ingredients)) {
                    foreach ($ingredients as $ingredient) {
                        echo '<li class="transition-all duration-300 hover:text-gray-900 hover:scale-105">' . esc_html($ingredient) . '</li>';
                    }
                }
                ?>
            </ul>
        </div>

        <div class="preparation-steps mb-12 bg-gradient-to-r from-green-200 via-yellow-200 to-red-200 p-6 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-xl">
            <h2 class="text-3xl font-semibold text-gray-800 mb-4 animate__animated animate__fadeIn">Preparation Steps</h2>
            <ol class="list-decimal pl-5 space-y-3 text-gray-700">
                <?php
                $steps = get_post_meta(get_the_ID(), 'recipe_steps', true);
                if (!empty($steps)) {
                    foreach ($steps as $step) {
                        echo '<li class="transition-all duration-300 hover:text-gray-900 hover:scale-105">' . esc_html($step) . '</li>';
                    }
                }
                ?>
            </ol>
        </div>

    </div>

</div>
<?php get_footer(); ?>
