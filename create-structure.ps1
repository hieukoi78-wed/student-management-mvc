# Script tao bo khung MVC thuan PHP.
# Chay tai thu muc goc cua du an.

$folders = @(
    "config",
    "routes",
    "app/models",
    "app/controllers",
    "app/views/layouts",
    "app/views/auth",
    "app/views/students",
    "public/css",
    "public/js"
)

$files = @(
    "config/DataBase.php",
    "routes/Router.php",
    "app/models/UserModel.php",
    "app/models/MajorModel.php",
    "app/models/ClassModel.php",
    "app/models/StudentModel.php",
    "app/controllers/AuthController.php",
    "app/controllers/StudentController.php",
    "app/views/layouts/header.php",
    "app/views/layouts/footer.php",
    "app/views/layouts/sidebar.php",
    "app/views/auth/login.php",
    "app/views/students/index.php",
    "app/views/students/detail.php",
    "app/views/students/create.php",
    "app/views/students/edit.php",
    "public/css/style.css",
    "public/index.php",
    "README.md"
)

# Xac dinh thu muc goc dua tren vi tri cua file script.
# Nho do script van dung khi Terminal dang o thu muc khac.
$root = $PSScriptRoot

foreach ($folder in $folders) {
    $path = Join-Path $root $folder
    New-Item -ItemType Directory -Path $path -Force | Out-Null
}

foreach ($file in $files) {
    $path = Join-Path $root $file
    if (-not (Test-Path -LiteralPath $path)) {
        New-Item -ItemType File -Path $path | Out-Null
    }
}

Write-Host "Da tao xong cau truc MVC!"