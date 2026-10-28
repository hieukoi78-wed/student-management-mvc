# Hệ thống quản trị nhà sách

Ứng dụng quản trị một cửa hàng sách, xây dựng bằng **PHP 8.2+**, **CodeIgniter 4** và **MySQL/MariaDB**. Quản trị viên quản lý thể loại, đầu sách, nhập kho, bán hàng, báo cáo và chat với thành viên. Thành viên đăng ký tài khoản để chat trong phòng chung hoặc nhắn riêng với quản trị viên.

> Tên thư mục dự án ban đầu có chữ `student-management`, nhưng đây **không phải** ứng dụng quản lý sinh viên. Các chức năng đang chạy là quản trị nhà sách.

## Mục lục

- [Chức năng](#chức-năng)
- [Yêu cầu cài đặt](#yêu-cầu-cài-đặt)
- [Cài đặt trên Windows và XAMPP](#cài-đặt-trên-windows-và-xampp)
- [Tạo và nạp cơ sở dữ liệu](#tạo-và-nạp-cơ-sở-dữ-liệu)
- [Cấu hình ứng dụng](#cấu-hình-ứng-dụng)
- [Chạy ứng dụng](#chạy-ứng-dụng)
- [Controller và Route trong CodeIgniter 4](#controller-và-route-trong-codeigniter-4)
- [Tài khoản dùng thử](#tài-khoản-dùng-thử)
- [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)
- [Kiểm tra ứng dụng](#kiểm-tra-ứng-dụng)
- [Cấu trúc dự án](#cấu-trúc-dự-án)
- [Xử lý lỗi thường gặp](#xử-lý-lỗi-thường-gặp)
- [Lưu ý bảo mật và sao lưu](#lưu-ý-bảo-mật-và-sao-lưu)

## Chức năng

### Dành cho quản trị viên

- Đăng nhập và đăng xuất khu vực quản trị.
- Xem tổng quan số thể loại, đầu sách và sách tồn kho thấp.
- Thêm, sửa và ngừng sử dụng thể loại sách.
- Thêm đầu sách mới với thông tin thể loại, tác giả, ISBN, nhà xuất bản, năm xuất bản, giá, mô tả và số lượng tồn ban đầu.
- Sửa thông tin đầu sách. Số tồn hiện có không bị thay đổi khi sửa thông tin sách.
- Nhập thêm số lượng cho một đầu sách đã có. Mỗi lần nhập được ghi vào lịch sử kho.
- Tạo đơn bán tại quầy. Hệ thống kiểm tra tồn kho, ghi nhận đơn và trừ số lượng trong cùng giao dịch cơ sở dữ liệu.
- Xem báo cáo doanh thu theo tuần, tháng và quý; khung giờ trong tuần bán được nhiều cuốn nhất; top 5 sách bán chạy trong tuần; số cuốn bán theo từng giờ.
- Chat với thành viên trong phòng chung hoặc trao đổi riêng với từng thành viên.

### Dành cho thành viên

- Tự đăng ký và đăng nhập.
- Tham gia phòng chat chung với các thành viên.
- Nhắn tin riêng với quản trị viên.
- Tin nhắn mới được tải tự động khi đang mở trang chat. Cơ chế hiện tại dùng polling mỗi 3 giây, không phải WebSocket.

## Yêu cầu cài đặt

1. **PHP 8.2 trở lên.** Các extension cần thiết gồm `intl`, `mbstring`, `mysqli` và `mysqlnd`.
2. **Composer 2** để cài thư viện PHP.
3. **MySQL hoặc MariaDB** đang chạy. Có thể sử dụng XAMPP.
4. Windows PowerShell hoặc Command Prompt.
5. Trình duyệt web hiện đại.

> Apache không bắt buộc nếu chạy bằng máy chủ phát triển `spark serve`. Nếu Apache XAMPP vẫn dùng PHP 8.0, hãy chạy ứng dụng bằng PHP CLI 8.2+ như hướng dẫn bên dưới; không dùng PHP 8.0 để chạy CodeIgniter của dự án này.

## Cài đặt trên Windows và XAMPP

### 1. Lấy mã nguồn

Tải hoặc giải nén toàn bộ dự án vào một thư mục dễ tìm. Ví dụ:

```text
C:\xampp\htdocs\student-management-mvc-main
```

Mở thư mục này bằng VS Code (**File → Open Folder...**). Mở Terminal mới trong VS Code và chuyển đến thư mục dự án nếu Terminal chưa ở đúng vị trí:

```powershell
cd C:\xampp\htdocs\student-management-mvc-main
```

Thay đường dẫn ví dụ trên nếu bạn đặt dự án ở nơi khác. Các lệnh cài đặt, Composer và `spark` cần được chạy tại thư mục có `composer.json` và file `spark`.

### 2. Bật dịch vụ cơ sở dữ liệu

Mở **XAMPP Control Panel** và nhấn **Start** tại MySQL. Apache chỉ cần bật nếu bạn đã cấu hình Apache chạy PHP 8.2+; khi dùng `spark serve`, có thể để Apache tắt.

### 3. Kiểm tra PHP CLI

Trong Terminal, kiểm tra phiên bản và các extension:

```powershell
php -v
php -m
```

Kết quả của `php -v` phải là PHP 8.2 trở lên. Trong danh sách `php -m` cần có `intl`, `mbstring` và `mysqli`.

Nếu máy có nhiều phiên bản PHP, hãy dùng đường dẫn đến PHP 8.2+ trong các lệnh sau. Ví dụ, nếu PHP 8.2 được cài tại `C:\xampp\php82`:

```powershell
& 'C:\xampp\php82\php.exe' -v
```

Trong trường hợp đó, thay `php` bằng `& 'C:\xampp\php82\php.exe'` ở những lệnh PHP phía dưới. Đường dẫn `C:\xampp\php82` chỉ là ví dụ; hãy dùng đường dẫn cài PHP thực tế trên máy của bạn.

### 4. Cài thư viện dự án

Kiểm tra Composer:

```powershell
composer --version
```

Cài các dependency của dự án:

```powershell
composer install
```

Lệnh này tạo thư mục `vendor/`. Không xóa hoặc gửi thư mục `vendor/` thay cho mã nguồn; người nhận có thể chạy lại `composer install`.

## Tạo và nạp cơ sở dữ liệu

### Cách khuyến nghị: dùng phpMyAdmin

1. Khi MySQL đang chạy, mở `http://localhost/phpmyadmin`.
2. Chọn **Import**.
3. Chọn file `database/online_shop.sql` trong thư mục dự án.
4. Nhấn **Import** hoặc **Go** để chạy.
5. Xác nhận đã có cơ sở dữ liệu tên `online_shop`.

File này tạo cơ sở dữ liệu, các bảng cần thiết và một số dữ liệu mẫu để đăng nhập/thử chức năng.

### Tùy chọn: nạp bằng MySQL CLI

Mở **Command Prompt** (không phải PowerShell) tại thư mục gốc dự án và chạy:

```bat
C:\xampp\mysql\bin\mysql.exe -u root -p < database\online_shop.sql
```

Nếu MySQL không đặt mật khẩu cho `root`, bỏ `-p`:

```bat
C:\xampp\mysql\bin\mysql.exe -u root < database\online_shop.sql
```

Nếu MySQL yêu cầu mật khẩu, nhập mật khẩu của chính máy đó khi được hỏi. Không ghi mật khẩu MySQL vào README, mã nguồn hoặc tin nhắn gửi chung.

### Nếu đã có database từ phiên bản quản trị sách trước

Không nạp `online_shop.sql` lên trên dữ liệu hiện có khi chưa sao lưu. Trước tiên xuất bản sao database bằng phpMyAdmin (**Export**). Nếu database hiện tại đã có các bảng cũ `admins`, `categories` và `books`, có thể chạy file nâng cấp `database/upgrade_bookstore_features.sql` để bổ sung chức năng nhập kho, bán hàng và chat:

```bat
C:\xampp\mysql\bin\mysql.exe -u root -p online_shop < database\upgrade_bookstore_features.sql
```

File nâng cấp được thiết kế cho schema quản trị sách cũ, **không phải** để khởi tạo database trống. Không chạy file nâng cấp lặp lại trên database không rõ trạng thái; hãy sao lưu và kiểm tra trước.

## Cấu hình ứng dụng

### 1. Tạo file `.env`

Tại thư mục gốc dự án, tạo cấu hình local từ file mẫu:

```powershell
Copy-Item .\env .\.env
```

Nếu `.env` đã tồn tại, không ghi đè cấu hình đang dùng. Mở `.env` bằng VS Code và chỉnh các giá trị phù hợp với máy của bạn:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8081/'

database.default.hostname = localhost
database.default.database = online_shop
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

- Thay `database.default.username` và `database.default.password` theo tài khoản MySQL trên máy của bạn.
- Giữ tên database là `online_shop` nếu bạn đã nạp schema theo hướng dẫn.
- Nếu đổi cổng hoặc địa chỉ chạy ứng dụng, cập nhật `app.baseURL` cho khớp.
- Không gửi file `.env` cho người khác nếu file có chứa mật khẩu hoặc khóa riêng. File này được Git bỏ qua; mỗi người tạo `.env` riêng trên máy mình.

## Chạy ứng dụng

Tại thư mục dự án, chạy máy chủ phát triển bằng PHP 8.2+:

```powershell
php spark serve --host 127.0.0.1 --port 8081
```

Nếu lệnh `php` không được nhận diện trong PowerShell và PHP 8.2 được cài tại `C:\xampp\php82`, dùng đường dẫn đầy đủ:

```powershell
& "C:\xampp\php82\php.exe" spark serve --host 127.0.0.1 --port 8081
```

Giữ cửa sổ Terminal đang chạy. Mở trình duyệt:

- Đăng nhập quản trị: [http://localhost:8081/login](http://localhost:8081/login)
- Đăng nhập thành viên: [http://localhost:8081/member/login](http://localhost:8081/member/login)
- Đăng ký thành viên: [http://localhost:8081/member/register](http://localhost:8081/member/register)

Để dừng máy chủ, quay lại cửa sổ Terminal và nhấn **Ctrl + C**.

Nếu cổng `8081` đang được sử dụng, có thể chạy bằng cổng khác, ví dụ:

```powershell
php spark serve --host 127.0.0.1 --port 8081
```

Sau đó đổi `app.baseURL` trong `.env` thành `http://localhost:8082/`.

### Chạy bằng Apache XAMPP

Chỉ dùng cách này khi Apache được cấu hình với **PHP 8.2 trở lên**. Apache XAMPP mặc định có thể vẫn chạy phiên bản PHP khác với PHP CLI; kiểm tra phiên bản PHP mà Apache thực sự sử dụng trước khi chạy.

Ứng dụng CodeIgniter cần web root trỏ đến thư mục `public/`. Không đặt `app/`, `vendor/`, `writable/` hoặc file cấu hình làm thư mục công khai. Khi truy cập qua Apache, đặt `app.baseURL` trong `.env` theo đúng địa chỉ và đường dẫn Apache đã cấu hình.

## Controller và Route trong CodeIgniter 4

Ứng dụng dùng mô hình MVC của **CodeIgniter 4**. Luồng xử lý một yêu cầu:

```text
Trình duyệt
  -> Route trong app/Config/Routes.php
  -> Filter kiểm tra quyền truy cập
  -> Controller nhận yêu cầu và điều phối
  -> Model đọc/ghi cơ sở dữ liệu
  -> View hiển thị HTML
  -> Trình duyệt
```

`public/index.php` là điểm vào web của CodeIgniter. Không cần tự tạo front controller hoặc router PHP thuần. Dự án tắt Auto Routing; chỉ các route khai báo trong `app/Config/Routes.php` mới được phục vụ.

### 6.1. Tạo và viết Controller

Controller nằm trong `app/Controllers/`, thuộc namespace `App\Controllers` và thường kế thừa `BaseController`. Mỗi phương thức public xử lý một hành động, nhận dữ liệu từ request, gọi model, sau đó trả về một view hoặc redirect.

Ví dụ phương thức liệt kê thể loại trong `app/Controllers/CategoriesController.php`:

```php
<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class CategoriesController extends BaseController
{
    public function index()
    {
        $categories = (new CategoryModel())
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('categories/index', [
            'categories' => $categories,
        ]);
    }
}
```

Trong ví dụ này:

- `CategoryModel` làm việc với bảng `categories`.
- `findAll()` lấy danh sách thể loại; `orderBy()` sắp xếp theo mã giảm dần.
- `view('categories/index', ...)` hiển thị `app/Views/categories/index.php`.
- Dữ liệu trong mảng truyền vào `view()` có thể được sử dụng trong template dưới tên `$categories`.

Các Controller nghiệp vụ hiện có:

| Controller | Trách nhiệm |
|---|---|
| `DashboardController` | Hiển thị tổng quan quản trị |
| `CategoriesController` | Thêm, xem, sửa và xóa thể loại |
| `BooksController` | Quản lý đầu sách và tồn kho ban đầu |
| `InventoryController` | Nhập thêm số lượng và lưu lịch sử kho |
| `SalesController` | Tạo đơn bán, kiểm tra tồn kho và trừ kho |
| `ReportsController` | Tổng hợp doanh thu và số lượng sách bán |
| `ChatController` | Phòng chat chung và chat riêng với quản trị viên |
| `MembersController` | Đăng ký, đăng nhập và đăng xuất thành viên |
| `AuthController` | Đăng nhập và đăng xuất quản trị viên |

Khi viết Controller, cần kiểm tra dữ liệu đầu vào trước khi lưu; dùng Model hoặc Query Builder, không nối dữ liệu người dùng trực tiếp vào SQL. Những thao tác ghi qua nhiều bảng, như tạo đơn bán và cập nhật tồn kho, phải nằm trong transaction để tránh lưu dữ liệu dở dang.

### 6.2. Tạo và viết Route

Route được khai báo tường minh trong `app/Config/Routes.php` theo dạng:

```php
$routes->get('duong-dan', 'TenController::tenPhuongThuc');
$routes->post('duong-dan', 'TenController::tenPhuongThuc');
```

- `get` thường dùng để mở trang hoặc đọc dữ liệu.
- `post` thường dùng để gửi biểu mẫu làm thay đổi dữ liệu.
- Tên Controller không cần phần mở rộng `.php`; tên phương thức được phân cách bằng `::`.

Ví dụ khai báo các route quản lý thể loại trong dự án:

```php
$routes->group('', ['filter' => 'admin'], static function (RouteCollection $routes): void {
    $routes->get('categories', 'CategoriesController::index');
    $routes->get('categories/create', 'CategoriesController::create');
    $routes->post('categories', 'CategoriesController::store');
    $routes->get('categories/(:num)/edit', 'CategoriesController::edit/$1');
    $routes->post('categories/(:num)', 'CategoriesController::update/$1');
    $routes->post('categories/(:num)/delete', 'CategoriesController::delete/$1');
});
```

Các route lần lượt hiển thị danh sách, mở form thêm, lưu thể loại mới, mở form sửa, cập nhật và xóa. `(:num)` chỉ khớp một đoạn URL dạng số; `$1` truyền giá trị này vào tham số `$id` của phương thức Controller.

Nhóm route có `filter => admin` chỉ dành cho quản trị viên đã đăng nhập. Chat sử dụng filter `chat` để yêu cầu đăng nhập bằng tài khoản quản trị hoặc thành viên. Các alias filter được đăng ký tại `app/Config/Filters.php`.

Để xem các route CodeIgniter đã đăng ký, chạy ở thư mục gốc dự án:

```powershell
php spark routes
```

Khi thêm chức năng mới, hãy tạo phương thức trong Controller, khai báo route tường minh, áp dụng filter phù hợp, rồi tạo hoặc cập nhật View và Model cần thiết. Không bật Auto Routing chỉ để bỏ qua khai báo route; route tường minh giúp kiểm soát các phương thức nào có thể được gọi từ web.

## Tài khoản dùng thử

Sau khi nạp `database/online_shop.sql`, dữ liệu mẫu gồm:

| Vai trò | Tên đăng nhập | Mật khẩu ban đầu |
|---|---|---|
| Quản trị viên | `admin` | `Admin@123456` |
| Thành viên mẫu | `thanhvien1` | `Admin@123456` |

Đây là thông tin mẫu dành cho chạy thử local. Đổi mật khẩu trước khi sử dụng trên môi trường dùng chung hoặc đưa lên máy chủ thật. Thành viên mới có thể tạo tài khoản tại `/member/register`.

## Hướng dẫn sử dụng

### Thêm thể loại và đầu sách mới

1. Đăng nhập bằng tài khoản quản trị.
2. Mở **Sách** → **Thêm sách**.
3. Nhập tên sách, tác giả, ISBN, giá và thông tin khác.
4. Tại ô **Thể loại**, gõ tên thể loại mới hoặc chọn một gợi ý đang có. Nếu tên đó chưa tồn tại, hệ thống tự tạo thể loại khi lưu sách.
5. Nhập **Tồn kho ban đầu** nếu sách mới đã có sẵn trong kho.
6. Lưu sách. Số lượng ban đầu được ghi nhận vào lịch sử tồn kho.

Muốn xem hoặc chỉnh sửa danh sách thể loại, mở **Sách** → **Quản lý thể loại**.

### Nhập thêm số lượng cho sách đã có

1. Mở **Nhập kho** → **Nhập thêm sách**.
2. Chọn đầu sách có trong danh sách.
3. Nhập số lượng và ghi chú hoặc số phiếu nhập nếu cần.
4. Ghi nhận giao dịch. Số lượng mới được cộng vào tồn kho và lưu lịch sử.

Không thay đổi tồn kho bằng cách sửa thông tin đầu sách; hãy dùng chức năng **Nhập kho** để giữ lịch sử chính xác.

Nếu sách chưa có trong hệ thống, mở mục **Sách** → **Thêm sách** để tạo đầu sách và nhập tồn kho ban đầu. Sau khi lưu, đầu sách có hàng và trạng thái đang bán sẽ xuất hiện trong lựa chọn ở màn hình bán hàng. Màn hình nhập kho chỉ dùng để cộng số lượng cho đầu sách đã tồn tại; nếu chưa có sách nào, trang này có nút dẫn đến form thêm sách.

### Bán sách tại quầy

1. Mở **Bán hàng** → **Tạo đơn bán**.
2. Có thể nhập tên khách hàng hoặc để trống cho khách lẻ.
3. Chọn một hoặc nhiều đầu sách và số lượng.
4. Xác nhận hoàn tất đơn.

Đơn bán chỉ được ghi nhận khi tồn kho đủ. Hệ thống lưu thông tin sách và giá tại thời điểm bán, sau đó cập nhật tồn kho. Báo cáo doanh thu chỉ tính đơn có trạng thái hoàn tất.

### Xem báo cáo

Mở **Báo cáo** để xem doanh thu tuần, tháng và quý; giờ bán được nhiều cuốn nhất trong tuần; top 5 sách bán chạy trong tuần và tổng số cuốn bán theo từng giờ. Tuần báo cáo bắt đầu từ thứ Hai; thời gian hiển thị và tính toán theo múi giờ `Asia/Ho_Chi_Minh`.

### Chat

- Thành viên đăng ký/đăng nhập để vào phòng chat chung.
- Quản trị viên đăng nhập và mở **Tin nhắn** để xem phòng chung hoặc chọn một thành viên để nhắn riêng.
- Thành viên mở mục chat riêng với quản trị viên để trao đổi trực tiếp.
- Tin nhắn được làm mới tự động khi trang chat đang mở; cần có kết nối đến máy chủ.

## Kiểm tra ứng dụng

Tại thư mục gốc dự án, có thể kiểm tra các route CodeIgniter:

```powershell
php spark routes
```

Chạy bộ kiểm thử PHPUnit (các test database mẫu dùng SQLite; PHP CLI cần bật extension SQLite):

```powershell
php vendor\bin\phpunit --no-coverage tests
```

Các trang quản trị yêu cầu đăng nhập. Nếu mở trực tiếp URL quản trị khi chưa đăng nhập, ứng dụng sẽ chuyển đến trang đăng nhập.

## Cấu trúc dự án

```text
app/
├── Config/          Cấu hình CodeIgniter và route
├── Controllers/     Xử lý yêu cầu, điều phối nghiệp vụ
├── Filters/         Bảo vệ khu vực admin, thành viên và chat
├── Models/          Truy vấn cơ sở dữ liệu
└── Views/           Giao diện đăng nhập, dashboard, sách, kho, bán hàng, báo cáo, chat
database/
├── online_shop.sql                  Tạo database mới và dữ liệu mẫu
└── upgrade_bookstore_features.sql   Nâng cấp schema quản trị sách cũ
public/              Điểm vào web và CSS/JS công khai
tests/               Kiểm thử PHPUnit
vendor/              Thư viện Composer (được tạo bởi composer install)
writable/            Cache, log, session và file runtime
env                  File cấu hình mẫu; dùng để tạo .env local
.env                 Cấu hình riêng của từng máy; không chia sẻ
spark                Công cụ dòng lệnh CodeIgniter
composer.json        Khai báo phiên bản PHP và dependency
```

Ứng dụng dùng cấu trúc CodeIgniter 4 trong `app/`; không còn mã MVC PHP thuần quản lý sinh viên trong dự án.

## Xử lý lỗi thường gặp

### `Your PHP version must be 8.2 or higher`

Bạn đang chạy bằng PHP cũ (thường là PHP của Apache XAMPP). Kiểm tra `php -v` trong Terminal và dùng PHP CLI 8.2+ để chạy `spark serve`. Nếu dùng Apache, cần cấu hình Apache chạy PHP 8.2+; chỉ đổi PHP CLI không làm Apache tự đổi phiên bản.

### `Unknown database 'online_shop'`

MySQL chưa chạy, database chưa được tạo, hoặc `.env` đang dùng sai tên database. Khởi động MySQL trong XAMPP, nạp `database/online_shop.sql`, rồi kiểm tra `database.default.database`, username, password và port trong `.env`.

### `Access denied for user`

Username hoặc mật khẩu trong `.env` không trùng với tài khoản MySQL trên máy. Cập nhật các giá trị này; không chia sẻ mật khẩu trong ảnh chụp hoặc tài liệu gửi đi.

### Trang báo `404 Not Found`

- Nếu dùng `spark serve`, kiểm tra Terminal máy chủ còn chạy hay không và truy cập đúng cổng, ví dụ `http://localhost:8081/login`.
- Nếu dùng Apache, cấu hình DocumentRoot phải trỏ đúng đến `public/` và bật rewrite theo cấu hình CodeIgniter. Máy chủ phát triển thường dễ chạy hơn khi mới cài.
- Kiểm tra rằng Composer đã cài dependency bằng `composer install`.

### Lỗi extension PHP như `intl`, `mbstring`, `mysqli` hoặc `sqlite3`

Chạy `php -m` để xem extension của đúng PHP CLI đang dùng. Mở file `php.ini` tương ứng với phiên bản PHP đó, bật extension được báo thiếu, rồi khởi động lại Terminal/máy chủ. Extension của PHP CLI và Apache có thể nằm trong các file `php.ini` khác nhau.

### Lỗi kết nối database hoặc lỗi sau khi sửa `.env`

Xác nhận `.env` nằm tại thư mục gốc cạnh file `spark`, kiểm tra cú pháp `tên = giá trị`, và bảo đảm MySQL đang chạy. Dừng rồi chạy lại `spark serve` sau khi thay đổi cấu hình.

## Lưu ý bảo mật và sao lưu

- Không gửi file `.env`, mật khẩu database, khóa riêng hoặc dữ liệu khách hàng cho người khác.
- Đổi tài khoản mẫu trước khi triển khai hoặc chia sẻ máy chủ cho người khác.
- Không bật `CI_ENVIRONMENT = development` trên máy chủ công khai; môi trường thật cần cấu hình production và HTTPS.
- Trước khi chạy SQL nâng cấp hoặc thay đổi dữ liệu, xuất bản sao database. Không dùng dữ liệu mẫu này như dữ liệu sản xuất.
- Không xóa đầu sách đã có lịch sử nhập kho hoặc bán hàng; hãy chuyển sách sang trạng thái ngừng bán để giữ lịch sử. Không thể xóa thể loại đang được sách sử dụng.
- Chỉ chia sẻ mã nguồn và các file hướng dẫn cần thiết; mỗi người cài dependency, tạo `.env` và dùng tài khoản database riêng trên máy của họ.
# student-management-mvc-main
