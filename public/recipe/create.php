 
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