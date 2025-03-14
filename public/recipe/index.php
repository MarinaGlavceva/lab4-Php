<?php
// Проверяем наличие файла с рецептами
if (!file_exists($filename) || filesize($filename) == 0) {
    echo "<p>Нет доступных рецептов. Добавьте новый рецепт!</p>";
} else {
    // Читаем все рецепты из файла
    $recipes = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recipes = array_map('json_decode', $recipes);
    
    if (empty($recipes)) {
        echo "<p>Ошибка загрузки рецептов. Попробуйте позже.</p>";
    } else {
        echo "<h2>Все рецепты:</h2>";
        foreach ($recipes as $recipe) {
            echo "<h3>{$recipe->title}</h3>";
            echo "<p><strong>Категория:</strong> {$recipe->category}</p>";
            echo "<p><strong>Ингредиенты:</strong> {$recipe->ingredients}</p>";
            echo "<p><strong>Описание:</strong> {$recipe->description}</p>";
            echo "<hr>";
        }
    }
}
?>
