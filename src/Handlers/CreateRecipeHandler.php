<?php
// Подключение вспомогательных функций
require_once '../helpers.php';

// Проверяем, что запрос был отправлен методом POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Фильтрация и получение данных из формы
    $title = sanitize($_POST['title'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $ingredients = sanitize($_POST['ingredients'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $tags = $_POST['tags'] ?? [];
    $steps = $_POST['steps'] ?? [];
    
    // Валидация данных
    $errors = validateRecipe($title, $category, $ingredients, $description, $steps);
    
    if (!empty($errors)) {
        // Если есть ошибки, сохраняем их в сессию и перенаправляем обратно на страницу формы
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: ../../public/recipe/create.php");
        exit;
    }
    
    // Создание массива рецепта
    $recipe = [
        'title' => $title,
        'category' => $category,
        'ingredients' => $ingredients,
        'description' => $description,
        'tags' => $tags,
        'steps' => $steps,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Сохранение данных в файл storage/recipes.txt в формате JSON
   // Сохранение данных в файл storage/recipes.txt в формате JSON
file_put_contents('../../storage/recipes.txt', json_encode($recipe, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

    
    // Перенаправление на главную страницу после успешного добавления
    header("Location: ../../public/index.php");
    exit;
}

/**
 * Фильтрация и защита данных
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Валидация данных формы
 * @param string $title
 * @param string $category
 * @param string $ingredients
 * @param string $description
 * @param array $steps
 * @return array
 */
function validateRecipe($title, $category, $ingredients, $description, $steps) {
    $errors = [];
    
    if (empty($title)) {
        $errors['title'] = 'Название рецепта обязательно';
    }
    if (empty($category)) {
        $errors['category'] = 'Выберите категорию рецепта';
    }
    if (empty($ingredients)) {
        $errors['ingredients'] = 'Ингредиенты не могут быть пустыми';
    }
    if (empty($description)) {
        $errors['description'] = 'Описание обязательно';
    }
    if (empty($steps)) {
        $errors['steps'] = 'Добавьте хотя бы один шаг приготовления';
    }
    
    return $errors;
}
?>