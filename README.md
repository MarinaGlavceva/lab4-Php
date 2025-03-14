 
# Лабораторная работа №4: Валидация форм в PHP

## 📌 Описание проекта
Этот проект представляет собой веб-приложение **"Каталог рецептов"**, в котором пользователи могут добавлять, просматривать и управлять рецептами. Основной целью работы является изучение **отправки данных форм на сервер**, их **валидации**, **сохранения в файл** и **отображения**.

## 📂 Задание 1. Создание проекта
### 📁 Организация файловой структуры
Для начала я создала корневую директорию проекта **recipe-book** и организовала удобную и логичную файловую структуру:
```
recipe-book/
├── public/                        # Публичная директория
│   ├── index.php                   # Главная страница (вывод последних рецептов)
│   └── recipe/                     
│       ├── create.php              # Форма добавления рецепта
│       └── index.php               # Страница с отображением всех рецептов
├── src/                            
│   ├── Handlers/                   
│   │   └── CreateRecipeHandler.php # Логика обработки формы
│   └── helpers.php                 # Вспомогательные функции
├── storage/                        
│   └── recipes.txt                 # Файл для хранения рецептов
└── README.md                       # Описание проекта
```

###  Как я создавала структуру

####  **Через Windows CMD** 
```cmd
mkdir recipe-book\public\recipe
mkdir recipe-book\src\Handlers
mkdir recipe-book\storage

cd recipe-book

echo. > public\index.php
echo. > public\recipe\create.php
echo. > public\recipe\index.php
echo. > src\Handlers\CreateRecipeHandler.php
echo. > src\helpers.php
echo. > storage\recipes.txt
echo. > README.md
```

# Задание 2. Создание формы добавления рецепта

## 📌 Описание
На странице **`public/recipe/create.php`** создала HTML-форма для добавления нового рецепта. 
Форма включает в себя все необходимые поля, включая динамическое добавление шагов приготовления.

## 📂 Файл: `public/recipe/create.php`

```html
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление рецепта</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 600px;
            margin: auto;
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
        input, select, textarea {
            padding: 8px;
            margin-top: 5px;
            width: 100%;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .steps-container {
            margin-top: 10px;
        }
        .step {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .step input {
            flex-grow: 1;
            margin-right: 10px;
        }
        .remove-step {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <h2>Добавить новый рецепт</h2>
    <form action="../../src/Handlers/CreateRecipeHandler.php" method="post">
        <label for="title">Название рецепта:</label>
        <input type="text" name="title" required>
        
        <label for="category">Категория:</label>
        <select name="category" required>
            <option value="Завтрак">Завтрак</option>
            <option value="Обед">Обед</option>
            <option value="Ужин">Ужин</option>
        </select>
        
        <label for="ingredients">Ингредиенты:</label>
        <textarea name="ingredients" required></textarea>
        
        <label for="description">Описание:</label>
        <textarea name="description" required></textarea>
        
        <label for="tags">Теги:</label>
        <select name="tags[]" multiple>
            <option value="Вегетарианское">Вегетарианское</option>
            <option value="Быстрое">Быстрое</option>
            <option value="Здоровое">Здоровое</option>
        </select>
        
        <label for="steps">Шаги приготовления:</label>
        <div id="steps-container" class="steps-container"></div>
        <button type="button" onclick="addStep()">Добавить шаг</button>
        
        <button type="submit">Отправить</button>
    </form>
    
    <script>
        function addStep() {
            const container = document.getElementById("steps-container");
            const div = document.createElement("div");
            div.classList.add("step");
            div.innerHTML = `<input type="text" name="steps[]" placeholder="Введите шаг приготовления" required>
                             <button type="button" class="remove-step" onclick="removeStep(this)">X</button>`;
            container.appendChild(div);
        }
        
        function removeStep(button) {
            button.parentElement.remove();
        }
    </script>
</body>
</html>
```

# Задание 3. Обработка формы

## 📌 Описание
В файле **`src/Handlers/CreateRecipeHandler.php`** реализую обработка формы добавления рецепта:
- **Фильтрация данных** – очистка от нежелательных символов
- **Валидация** – проверка обязательных полей
- **Сохранение рецепта** в `storage/recipes.txt` в формате JSON
- **Перенаправление пользователя** после успешного сохранения
- **Вывод ошибок** на странице, если валидация не пройдена

## 📂 Файл: `src/Handlers/CreateRecipeHandler.php`

```php
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
    file_put_contents('../../storage/recipes.txt', json_encode($recipe) . PHP_EOL, FILE_APPEND);
    
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
```
# Задание 4. Отображение рецептов

## 📌 Описание
В файлах **`public/index.php`** и **`public/recipe/index.php`** реализовано отображение рецептов:
- **На главной (`index.php`)** – показываются **2 последних** рецепта.
- **На странице рецептов (`recipe/index.php`)** – отображаются **все рецепты**.
- Если рецепты отсутствуют, выводится сообщение **"Нет доступных рецептов"**.

## 📂 Файл: `public/index.php`

```php
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
```

## 📂 Файл: `public/recipe/index.php`

```php
<?php
// Путь к файлу с рецептами
$filename = '../../storage/recipes.txt';

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
```


## 📝 Контрольные вопросы
1. **Какие методы HTTP применяются для отправки данных формы?**
   - **GET**: Данные передаются в URL (например, `?name=John`).
   - **POST**: Данные передаются в теле запроса (более безопасно).

2. **Что такое валидация данных и чем она отличается от фильтрации?**
   - **Валидация** — проверка корректности данных (например, обязательные поля, ограничения по длине).
   - **Фильтрация** — удаление нежелательных символов (например, `filter_var()` для XSS-защиты).

3. **Какие функции PHP используются для фильтрации данных?**
   - `htmlspecialchars($data)`: Защита от XSS.
   - `trim($data)`: Удаление пробелов.
   - `filter_var($data, FILTER_SANITIZE_STRING)`: Фильтрация строк.

## 🚀 Итог
Этот проект **объединяет работу с HTML-формами, валидацию данных и обработку в PHP**.
