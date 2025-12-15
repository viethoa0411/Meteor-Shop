<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingDistance;

class ShippingDistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này tạo dữ liệu đầy đủ cho bảng shipping_distances
     * Khoảng cách tính từ Hà Nội - Nam Từ Liêm đến các quận/huyện miền Bắc
     * Sử dụng chuẩn hành chính Việt Nam cũ (trước khi sáp nhập)
     */
    public function run(): void
    {
        // Dữ liệu đầy đủ cho tất cả các tỉnh, quận/huyện miền Bắc
        // Format: ['Tỉnh/Thành phố', 'Quận/Huyện/Thị Xã', Số Km từ Hà Nội - Nam Từ Liêm]
        $distances = [
            // ============================================
            // HÀ NỘI (12 quận, 1 thị xã, 17 huyện)
            // Chuẩn hành chính cũ (trước khi sáp nhập Hà Tây)
            // ============================================
            ['Hà Nội', 'Quận Ba Đình', 8],
            ['Hà Nội', 'Quận Hoàn Kiếm', 10],
            ['Hà Nội', 'Quận Tây Hồ', 12],
            ['Hà Nội', 'Quận Long Biên', 15],
            ['Hà Nội', 'Quận Cầu Giấy', 5],
            ['Hà Nội', 'Quận Đống Đa', 7],
            ['Hà Nội', 'Quận Hai Bà Trưng', 8],
            ['Hà Nội', 'Quận Hoàng Mai', 12],
            ['Hà Nội', 'Quận Thanh Xuân', 6],
            ['Hà Nội', 'Quận Hà Đông', 15],
            ['Hà Nội', 'Quận Nam Từ Liêm', 0], // Điểm gốc
            ['Hà Nội', 'Quận Bắc Từ Liêm', 3],
            ['Hà Nội', 'Thị xã Sơn Tây', 35],
            ['Hà Nội', 'Huyện Sóc Sơn', 30],
            ['Hà Nội', 'Huyện Đông Anh', 20],
            ['Hà Nội', 'Huyện Gia Lâm', 18],
            ['Hà Nội', 'Huyện Mê Linh', 25],
            ['Hà Nội', 'Huyện Ba Vì', 50],
            ['Hà Nội', 'Huyện Phúc Thọ', 40],
            ['Hà Nội', 'Huyện Đan Phượng', 30],
            ['Hà Nội', 'Huyện Hoài Đức', 20],
            ['Hà Nội', 'Huyện Quốc Oai', 35],
            ['Hà Nội', 'Huyện Thạch Thất', 30],
            ['Hà Nội', 'Huyện Chương Mỹ', 35],
            ['Hà Nội', 'Huyện Thanh Oai', 25],
            ['Hà Nội', 'Huyện Thường Tín', 30],
            ['Hà Nội', 'Huyện Phú Xuyên', 40],
            ['Hà Nội', 'Huyện Ứng Hòa', 45],
            ['Hà Nội', 'Huyện Mỹ Đức', 50],

            // ============================================
            // HẢI PHÒNG (7 quận, 8 huyện)
            // ============================================
            ['Hải Phòng', 'Quận Hồng Bàng', 105],
            ['Hải Phòng', 'Quận Ngô Quyền', 105],
            ['Hải Phòng', 'Quận Lê Chân', 108],
            ['Hải Phòng', 'Quận Hải An', 110],
            ['Hải Phòng', 'Quận Kiến An', 115],
            ['Hải Phòng', 'Quận Đồ Sơn', 120],
            ['Hải Phòng', 'Quận Dương Kinh', 115],
            ['Hải Phòng', 'Huyện Thuỷ Nguyên', 110],
            ['Hải Phòng', 'Huyện An Dương', 108],
            ['Hải Phòng', 'Huyện An Lão', 115],
            ['Hải Phòng', 'Huyện Kiến Thuỵ', 120],
            ['Hải Phòng', 'Huyện Tiên Lãng', 125],
            ['Hải Phòng', 'Huyện Vĩnh Bảo', 130],
            ['Hải Phòng', 'Huyện Cát Hải', 150],
            ['Hải Phòng', 'Huyện Bạch Long Vĩ', 200],

            // ============================================
            // HẢI DƯƠNG (1 thành phố, 1 thị xã, 10 huyện)
            // ============================================
            ['Hải Dương', 'Thành phố Hải Dương', 58],
            ['Hải Dương', 'Thị xã Chí Linh', 65],
            ['Hải Dương', 'Huyện Nam Sách', 60],
            ['Hải Dương', 'Huyện Kinh Môn', 70],
            ['Hải Dương', 'Huyện Kim Thành', 75],
            ['Hải Dương', 'Huyện Thanh Hà', 65],
            ['Hải Dương', 'Huyện Cẩm Giàng', 60],
            ['Hải Dương', 'Huyện Bình Giang', 65],
            ['Hải Dương', 'Huyện Gia Lộc', 60],
            ['Hải Dương', 'Huyện Tứ Kỳ', 70],
            ['Hải Dương', 'Huyện Ninh Giang', 75],
            ['Hải Dương', 'Huyện Thanh Miện', 70],

            // ============================================
            // HƯNG YÊN (1 thành phố, 1 thị xã, 8 huyện)
            // ============================================
            ['Hưng Yên', 'Thành phố Hưng Yên', 64],
            ['Hưng Yên', 'Thị xã Mỹ Hào', 50],
            ['Hưng Yên', 'Huyện Văn Lâm', 55],
            ['Hưng Yên', 'Huyện Văn Giang', 50],
            ['Hưng Yên', 'Huyện Yên Mỹ', 60],
            ['Hưng Yên', 'Huyện Mỹ Hào', 50],
            ['Hưng Yên', 'Huyện Ân Thi', 70],
            ['Hưng Yên', 'Huyện Khoái Châu', 65],
            ['Hưng Yên', 'Huyện Kim Động', 70],
            ['Hưng Yên', 'Huyện Tiên Lữ', 75],
            ['Hưng Yên', 'Huyện Phù Cừ', 80],

            // ============================================
            // HÀ NAM (1 thành phố, 5 huyện)
            // ============================================
            ['Hà Nam', 'Thành phố Phủ Lý', 60],
            ['Hà Nam', 'Huyện Duy Tiên', 55],
            ['Hà Nam', 'Huyện Kim Bảng', 65],
            ['Hà Nam', 'Huyện Thanh Liêm', 70],
            ['Hà Nam', 'Huyện Bình Lục', 75],
            ['Hà Nam', 'Huyện Lý Nhân', 80],

            // ============================================
            // NAM ĐỊNH (1 thành phố, 9 huyện)
            // ============================================
            ['Nam Định', 'Thành phố Nam Định', 90],
            ['Nam Định', 'Huyện Mỹ Lộc', 88],
            ['Nam Định', 'Huyện Vụ Bản', 95],
            ['Nam Định', 'Huyện Ý Yên', 100],
            ['Nam Định', 'Huyện Nghĩa Hưng', 110],
            ['Nam Định', 'Huyện Nam Trực', 92],
            ['Nam Định', 'Huyện Trực Ninh', 100],
            ['Nam Định', 'Huyện Xuân Trường', 105],
            ['Nam Định', 'Huyện Giao Thủy', 110],
            ['Nam Định', 'Huyện Hải Hậu', 115],

            // ============================================
            // THÁI BÌNH (1 thành phố, 7 huyện)
            // ============================================
            ['Thái Bình', 'Thành phố Thái Bình', 110],
            ['Thái Bình', 'Huyện Quỳnh Phụ', 120],
            ['Thái Bình', 'Huyện Hưng Hà', 115],
            ['Thái Bình', 'Huyện Đông Hưng', 105],
            ['Thái Bình', 'Huyện Thái Thụy', 115],
            ['Thái Bình', 'Huyện Tiền Hải', 125],
            ['Thái Bình', 'Huyện Kiến Xương', 115],
            ['Thái Bình', 'Huyện Vũ Thư', 110],

            // ============================================
            // NINH BÌNH (1 thành phố, 1 thị xã, 6 huyện)
            // ============================================
            ['Ninh Bình', 'Thành phố Ninh Bình', 95],
            ['Ninh Bình', 'Thị xã Tam Điệp', 100],
            ['Ninh Bình', 'Huyện Nho Quan', 110],
            ['Ninh Bình', 'Huyện Gia Viễn', 105],
            ['Ninh Bình', 'Huyện Hoa Lư', 100],
            ['Ninh Bình', 'Huyện Yên Khánh', 100],
            ['Ninh Bình', 'Huyện Kim Sơn', 110],
            ['Ninh Bình', 'Huyện Yên Mô', 105],

            // ============================================
            // BẮC NINH (1 thành phố, 1 thị xã, 6 huyện)
            // ============================================
            ['Bắc Ninh', 'Thành phố Bắc Ninh', 30],
            ['Bắc Ninh', 'Thị xã Từ Sơn', 25],
            ['Bắc Ninh', 'Huyện Yên Phong', 35],
            ['Bắc Ninh', 'Huyện Quế Võ', 40],
            ['Bắc Ninh', 'Huyện Tiên Du', 35],
            ['Bắc Ninh', 'Huyện Gia Bình', 40],
            ['Bắc Ninh', 'Huyện Lương Tài', 45],

            // ============================================
            // BẮC GIANG (1 thành phố, 9 huyện)
            // ============================================
            ['Bắc Giang', 'Thành phố Bắc Giang', 50],
            ['Bắc Giang', 'Huyện Yên Thế', 60],
            ['Bắc Giang', 'Huyện Tân Yên', 55],
            ['Bắc Giang', 'Huyện Lạng Giang', 55],
            ['Bắc Giang', 'Huyện Lục Nam', 65],
            ['Bắc Giang', 'Huyện Lục Ngạn', 70],
            ['Bắc Giang', 'Huyện Sơn Động', 80],
            ['Bắc Giang', 'Huyện Yên Dũng', 50],
            ['Bắc Giang', 'Huyện Việt Yên', 45],
            ['Bắc Giang', 'Huyện Hiệp Hòa', 55],

            // ============================================
            // QUẢNG NINH (4 thành phố, 2 thị xã, 6 huyện)
            // ============================================
            ['Quảng Ninh', 'Thành phố Hạ Long', 150],
            ['Quảng Ninh', 'Thành phố Móng Cái', 200],
            ['Quảng Ninh', 'Thành phố Cẩm Phả', 160],
            ['Quảng Ninh', 'Thành phố Uông Bí', 130],
            ['Quảng Ninh', 'Thị xã Bình Liêu', 210],
            ['Quảng Ninh', 'Thị xã Đông Triều', 140],
            ['Quảng Ninh', 'Huyện Vân Đồn', 180],
            ['Quảng Ninh', 'Huyện Ba Chẽ', 190],
            ['Quảng Ninh', 'Huyện Cô Tô', 220],
            ['Quảng Ninh', 'Huyện Hải Hà', 200],
            ['Quảng Ninh', 'Huyện Tiên Yên', 190],
            ['Quảng Ninh', 'Huyện Đầm Hà', 195],

            // ============================================
            // LÀO CAI (1 thành phố, 1 thị xã, 7 huyện)
            // ============================================
            ['Lào Cai', 'Thành phố Lào Cai', 320],
            ['Lào Cai', 'Thị xã Sa Pa', 340],
            ['Lào Cai', 'Huyện Bát Xát', 330],
            ['Lào Cai', 'Huyện Mường Khương', 340],
            ['Lào Cai', 'Huyện Si Ma Cai', 350],
            ['Lào Cai', 'Huyện Bắc Hà', 360],
            ['Lào Cai', 'Huyện Bảo Thắng', 310],
            ['Lào Cai', 'Huyện Bảo Yên', 300],
            ['Lào Cai', 'Huyện Văn Bàn', 280],

            // ============================================
            // YÊN BÁI (1 thành phố, 1 thị xã, 7 huyện)
            // ============================================
            ['Yên Bái', 'Thành phố Yên Bái', 180],
            ['Yên Bái', 'Thị xã Nghĩa Lộ', 200],
            ['Yên Bái', 'Huyện Lục Yên', 200],
            ['Yên Bái', 'Huyện Văn Yên', 190],
            ['Yên Bái', 'Huyện Mù Cang Chải', 250],
            ['Yên Bái', 'Huyện Trấn Yên', 185],
            ['Yên Bái', 'Huyện Trạm Tấu', 220],
            ['Yên Bái', 'Huyện Văn Chấn', 210],
            ['Yên Bái', 'Huyện Yên Bình', 170],

            // ============================================
            // TUYÊN QUANG (1 thành phố, 6 huyện)
            // ============================================
            ['Tuyên Quang', 'Thành phố Tuyên Quang', 150],
            ['Tuyên Quang', 'Huyện Lâm Bình', 200],
            ['Tuyên Quang', 'Huyện Na Hang', 190],
            ['Tuyên Quang', 'Huyện Chiêm Hóa', 170],
            ['Tuyên Quang', 'Huyện Hàm Yên', 160],
            ['Tuyên Quang', 'Huyện Yên Sơn', 155],
            ['Tuyên Quang', 'Huyện Sơn Dương', 165],

            // ============================================
            // LẠNG SƠN (1 thành phố, 10 huyện)
            // ============================================
            ['Lạng Sơn', 'Thành phố Lạng Sơn', 150],
            ['Lạng Sơn', 'Huyện Tràng Định', 180],
            ['Lạng Sơn', 'Huyện Bình Gia', 170],
            ['Lạng Sơn', 'Huyện Văn Lãng', 175],
            ['Lạng Sơn', 'Huyện Cao Lộc', 160],
            ['Lạng Sơn', 'Huyện Văn Quan', 180],
            ['Lạng Sơn', 'Huyện Bắc Sơn', 190],
            ['Lạng Sơn', 'Huyện Hữu Lũng', 140],
            ['Lạng Sơn', 'Huyện Chi Lăng', 145],
            ['Lạng Sơn', 'Huyện Lộc Bình', 200],
            ['Lạng Sơn', 'Huyện Đình Lập', 210],

            // ============================================
            // CAO BẰNG (1 thành phố, 12 huyện)
            // ============================================
            ['Cao Bằng', 'Thành phố Cao Bằng', 280],
            ['Cao Bằng', 'Huyện Bảo Lâm', 300],
            ['Cao Bằng', 'Huyện Bảo Lạc', 310],
            ['Cao Bằng', 'Huyện Hà Quảng', 290],
            ['Cao Bằng', 'Huyện Trùng Khánh', 300],
            ['Cao Bằng', 'Huyện Hạ Lang', 310],
            ['Cao Bằng', 'Huyện Quảng Uyên', 295],
            ['Cao Bằng', 'Huyện Phục Hòa', 305],
            ['Cao Bằng', 'Huyện Hòa An', 285],
            ['Cao Bằng', 'Huyện Nguyên Bình', 270],
            ['Cao Bằng', 'Huyện Thạch An', 320],
            ['Cao Bằng', 'Huyện Trà Lĩnh', 305],

            // ============================================
            // BẮC KẠN (1 thành phố, 7 huyện)
            // ============================================
            ['Bắc Kạn', 'Thành phố Bắc Kạn', 160],
            ['Bắc Kạn', 'Huyện Pác Nặm', 200],
            ['Bắc Kạn', 'Huyện Ba Bể', 180],
            ['Bắc Kạn', 'Huyện Ngân Sơn', 190],
            ['Bắc Kạn', 'Huyện Bạch Thông', 170],
            ['Bắc Kạn', 'Huyện Chợ Đồn', 175],
            ['Bắc Kạn', 'Huyện Chợ Mới', 165],
            ['Bắc Kạn', 'Huyện Na Rì', 185],

            // ============================================
            // THÁI NGUYÊN (2 thành phố, 7 huyện)
            // ============================================
            ['Thái Nguyên', 'Thành phố Thái Nguyên', 80],
            ['Thái Nguyên', 'Thành phố Sông Công', 85],
            ['Thái Nguyên', 'Huyện Định Hóa', 100],
            ['Thái Nguyên', 'Huyện Phú Lương', 90],
            ['Thái Nguyên', 'Huyện Đồng Hỷ', 85],
            ['Thái Nguyên', 'Huyện Võ Nhai', 110],
            ['Thái Nguyên', 'Huyện Đại Từ', 95],
            ['Thái Nguyên', 'Huyện Phú Bình', 90],
            ['Thái Nguyên', 'Huyện Phổ Yên', 85],

            // ============================================
            // PHÚ THỌ (1 thành phố, 1 thị xã, 11 huyện)
            // ============================================
            ['Phú Thọ', 'Thành phố Việt Trì', 80],
            ['Phú Thọ', 'Thị xã Phú Thọ', 90],
            ['Phú Thọ', 'Huyện Đoan Hùng', 120],
            ['Phú Thọ', 'Huyện Hạ Hòa', 100],
            ['Phú Thọ', 'Huyện Thanh Ba', 110],
            ['Phú Thọ', 'Huyện Phù Ninh', 85],
            ['Phú Thọ', 'Huyện Yên Lập', 130],
            ['Phú Thọ', 'Huyện Cẩm Khê', 95],
            ['Phú Thọ', 'Huyện Tam Nông', 100],
            ['Phú Thọ', 'Huyện Lâm Thao', 90],
            ['Phú Thọ', 'Huyện Thanh Sơn', 140],
            ['Phú Thọ', 'Huyện Thanh Thủy', 110],
            ['Phú Thọ', 'Huyện Tân Sơn', 150],

            // ============================================
            // VĨNH PHÚC (1 thành phố, 1 thị xã, 7 huyện)
            // ============================================
            ['Vĩnh Phúc', 'Thành phố Vĩnh Yên', 60],
            ['Vĩnh Phúc', 'Thị xã Phúc Yên', 50],
            ['Vĩnh Phúc', 'Huyện Lập Thạch', 80],
            ['Vĩnh Phúc', 'Huyện Tam Dương', 70],
            ['Vĩnh Phúc', 'Huyện Tam Đảo', 75],
            ['Vĩnh Phúc', 'Huyện Bình Xuyên', 65],
            ['Vĩnh Phúc', 'Huyện Yên Lạc', 70],
            ['Vĩnh Phúc', 'Huyện Vĩnh Tường', 75],
            ['Vĩnh Phúc', 'Huyện Sông Lô', 85],

            // ============================================
            // ĐIỆN BIÊN (1 thành phố, 1 thị xã, 8 huyện)
            // ============================================
            ['Điện Biên', 'Thành phố Điện Biên Phủ', 450],
            ['Điện Biên', 'Thị xã Mường Lay', 480],
            ['Điện Biên', 'Huyện Mường Nhé', 500],
            ['Điện Biên', 'Huyện Mường Chà', 470],
            ['Điện Biên', 'Huyện Tủa Chùa', 490],
            ['Điện Biên', 'Huyện Tuần Giáo', 460],
            ['Điện Biên', 'Huyện Điện Biên', 450],
            ['Điện Biên', 'Huyện Điện Biên Đông', 470],
            ['Điện Biên', 'Huyện Mường Ảng', 460],
            ['Điện Biên', 'Huyện Nậm Pồ', 510],

            // ============================================
            // LAI CHÂU (1 thành phố, 7 huyện)
            // ============================================
            ['Lai Châu', 'Thành phố Lai Châu', 420],
            ['Lai Châu', 'Huyện Tam Đường', 440],
            ['Lai Châu', 'Huyện Mường Tè', 480],
            ['Lai Châu', 'Huyện Sìn Hồ', 450],
            ['Lai Châu', 'Huyện Phong Thổ', 460],
            ['Lai Châu', 'Huyện Than Uyên', 400],
            ['Lai Châu', 'Huyện Tân Uyên', 410],
            ['Lai Châu', 'Huyện Nậm Nhùn', 490],

            // ============================================
            // SƠN LA (1 thành phố, 11 huyện)
            // ============================================
            ['Sơn La', 'Thành phố Sơn La', 320],
            ['Sơn La', 'Huyện Quỳnh Nhai', 350],
            ['Sơn La', 'Huyện Mường La', 340],
            ['Sơn La', 'Huyện Thuận Châu', 360],
            ['Sơn La', 'Huyện Mường Tè', 380],
            ['Sơn La', 'Huyện Sông Mã', 370],
            ['Sơn La', 'Huyện Sốp Cộp', 400],
            ['Sơn La', 'Huyện Yên Châu', 350],
            ['Sơn La', 'Huyện Mai Sơn', 330],
            ['Sơn La', 'Huyện Mộc Châu', 280],
            ['Sơn La', 'Huyện Mường Khương', 360],
            ['Sơn La', 'Huyện Vân Hồ', 340],

            // ============================================
            // HÒA BÌNH (1 thành phố, 9 huyện)
            // ============================================
            ['Hòa Bình', 'Thành phố Hòa Bình', 75],
            ['Hòa Bình', 'Huyện Đà Bắc', 90],
            ['Hòa Bình', 'Huyện Lương Sơn', 60],
            ['Hòa Bình', 'Huyện Kim Bôi', 85],
            ['Hòa Bình', 'Huyện Cao Phong', 80],
            ['Hòa Bình', 'Huyện Tân Lạc', 95],
            ['Hòa Bình', 'Huyện Mai Châu', 100],
            ['Hòa Bình', 'Huyện Lạc Sơn', 110],
            ['Hòa Bình', 'Huyện Yên Thủy', 105],
            ['Hòa Bình', 'Huyện Lạc Thủy', 120],
        ];

        // Thêm dữ liệu vào database
        $count = 0;
        foreach ($distances as $distance) {
            ShippingDistance::createOrUpdate(
                $distance[0], // province_name
                $distance[1], // district_name
                $distance[2]  // distance_km
            );
            $count++;
        }

        $this->command->info("✅ Đã thêm {$count} bản ghi khoảng cách vận chuyển vào database.");
        $this->command->info("📍 Khoảng cách tính từ Hà Nội - Nam Từ Liêm đến các quận/huyện miền Bắc.");
    }
}
