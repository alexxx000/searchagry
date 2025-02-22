Dct# Инициализация репозитория
git init

# Добавление всех файлов в staging
git add .

# Создание первого коммита
git commit -m "Initial commit: Basic project structure and functionality"

# Подключение к удаленному репозиторию (замените USERNAME и REPO_NAME на ваши данные)
git remote add origin https://github.com/alexxx000/searchagry.git

# Отправка изменений в удаленный репозиторий
git push -u origin main

Для отката к этой версии в будущем вы сможете использовать:
git reset --hard <commit-hash>
или 
git checkout <commit-hash>
-------------------------------------------------------------
# Добавить все изменения
git add .

# Создать коммит
git commit -m "Update documentation and add task history"

# Отправить изменения
git push