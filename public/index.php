 
<?php
/**
 * public/index.php
 * Главная страница - отображает 2 последних рецепта
 */

// Путь к файлу с рецептами
$filename = '../storage/recipes.txt';

// Проверяем, существует ли файл и содержит ли он данные
if (!file_exists($filename) || filesize($filename) == 0) {
    echo "<p>Пока нет ни одного рецепта. Добавьте свой первый рецепт!</p>";
} else {
    // Читаем данные из файла (пропуская пустые строки)
    $recipes = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Преобразуем JSON-строки в массив
    $recipes = array_map('json_decode', $recipes);

      // Фильтруем только корректные рецепты (избегаем null)
    $recipes = array_filter($recipes, function ($recipe) {
        return is_object($recipe) && isset($recipe->title);
    });

    // Если массив рецептов пуст (например, JSON-ошибка)
    if (empty($recipes)) {
        echo "<p>Ошибка загрузки рецептов. Попробуйте позже.</p>";
    } else {
        // Получаем два последних рецепта
        $latestRecipes = array_slice($recipes, -2);
        
        echo "<h2>Последние рецепты:</h2>";
        foreach ($latestRecipes as $recipe) {
            echo "<h3>{$recipe->title}</h3>";
            echo "<p><strong>Категория:</strong> {$recipe->category}</p>";
            echo "<p><strong>Ингредиенты:</strong> {$recipe->ingredients}</p>";
            echo "<p><strong>Описание:</strong> {$recipe->description}</p>";
            echo "<hr>";
        }
    }
}
?>
