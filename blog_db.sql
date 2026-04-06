-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 06, 2026 at 11:38 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `category_id` int NOT NULL,
  `author_id` int NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `excerpt`, `content`, `thumbnail`, `category_id`, `author_id`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Panduan Lengkap Belajar PHP untuk Pemula', 'panduan-lengkap-belajar-php-untuk-pemula', 'PHP adalah bahasa pemrograman server-side yang sangat populer untuk membangun website dinamis.', '<p>PHP (Hypertext Preprocessor) adalah bahasa scripting server-side yang banyak digunakan dalam pengembangan web. PHP dapat diintegrasikan dengan HTML dan berjalan di server.</p><h2>Mengapa Belajar PHP?</h2><p>PHP digunakan oleh lebih dari 79% website di seluruh dunia, termasuk Facebook, Wikipedia, dan WordPress. Dengan mempelajari PHP, Anda membuka peluang karir yang sangat luas.</p><h2>Langkah Pertama</h2><p>Mulailah dengan menginstall XAMPP atau WAMP di komputer Anda. Ini akan menyediakan lingkungan PHP, MySQL, dan Apache yang siap digunakan.</p><p>Pelajari sintaks dasar seperti variabel, kondisi, perulangan, dan fungsi. Kemudian lanjutkan ke topik yang lebih advanced seperti OOP, PDO, dan framework seperti Laravel.</p>', 'thumb_1775455338_69d34c6a0060e.jpg', 1, 1, 'published', 245, '2026-04-06 04:44:34', '2026-04-06 06:02:18'),
(2, '10 Tips Meningkatkan Produktivitas Kerja dari Rumah', '10-tips-meningkatkan-produktivitas-kerja-dari-rumah', 'Bekerja dari rumah memiliki tantangan tersendiri. Berikut 10 tips ampuh untuk tetap produktif.', '<p>Work from home (WFH) kini menjadi gaya kerja yang umum. Namun, tanpa disiplin yang tepat, produktivitas bisa menurun drastis.</p><h2>1. Buat Jadwal Rutin</h2><p>Tetapkan jam kerja yang konsisten setiap harinya. Mulai dan selesai pada waktu yang sama seperti di kantor.</p><h2>2. Siapkan Ruang Kerja Khusus</h2><p>Pisahkan area kerja dari area istirahat. Ini membantu otak untuk \"masuk mode kerja\" saat berada di area tersebut.</p><h2>3. Gunakan Teknik Pomodoro</h2><p>Kerja 25 menit, istirahat 5 menit. Teknik ini terbukti meningkatkan fokus dan mengurangi kelelahan mental.</p><h2>4. Hindari Distraksi Digital</h2><p>Matikan notifikasi media sosial selama jam kerja. Gunakan aplikasi seperti Forest atau Focus@Will.</p>', 'thumb_1775455553_69d34d41d5e57.jpg', 2, 2, 'published', 183, '2026-04-06 04:44:34', '2026-04-06 06:05:53'),
(3, 'Cara Memulai Bisnis Online di Era Digital', 'cara-memulai-bisnis-online-di-era-digital', 'Era digital membuka peluang bisnis yang tak terbatas. Pelajari langkah-langkah memulai bisnis online.', '<p>Bisnis online semakin diminati karena modal yang relatif kecil namun potensi keuntungan yang besar. Berikut panduan lengkap untuk memulai.</p><h2>Riset Pasar</h2><p>Sebelum memulai, lakukan riset mendalam tentang target pasar Anda. Gunakan Google Trends, survei online, dan analisis kompetitor.</p><h2>Pilih Model Bisnis</h2><p>Ada berbagai model bisnis online: dropshipping, affiliate marketing, produk digital, jasa, atau marketplace. Pilih yang sesuai dengan keahlian dan sumber daya Anda.</p><h2>Bangun Presence Online</h2><p>Buat website profesional, aktifkan media sosial yang relevan, dan optimalkan SEO untuk meningkatkan visibilitas bisnis Anda.</p>', 'thumb_1775455450_69d34cda542e5.jpg', 3, 1, 'published', 321, '2026-04-06 04:44:34', '2026-04-06 06:04:10'),
(4, 'Kebersamaan Tanpa Batas dalam Family Gathering Group Vertical Dreams', 'kebersamaan-tanpa-batas-dalam-family-gathering-group-vertical-dreams', 'Vertical Dreams menggelar kegiatan Family Gathering sebagai bentuk mempererat kebersamaan, memperkuat solidaritas, dan membangun semangat positif antar anggota serta keluarga. Acara ini diisi dengan berbagai kegiatan seru, penuh kehangatan, serta momen kebersamaan yang tak terlupakan.', 'Vertical Dreams kembali menunjukkan komitmennya dalam membangun hubungan yang harmonis antar anggota melalui kegiatan Family Gathering yang berlangsung dengan penuh keceriaan. Acara ini menjadi momen istimewa karena tidak hanya melibatkan para anggota, tetapi juga keluarga mereka, sehingga tercipta suasana yang hangat dan penuh keakraban.\r\n\r\nKegiatan Family Gathering ini diisi dengan berbagai aktivitas menarik seperti permainan kelompok, sesi ice breaking, lomba kekompakan keluarga, serta acara santai yang memungkinkan setiap peserta untuk saling mengenal lebih dekat. Gelak tawa dan semangat kebersamaan terasa begitu kuat sepanjang kegiatan berlangsung. Anak-anak pun turut menikmati berbagai permainan yang telah disiapkan, sementara para orang tua saling berbagi cerita dan pengalaman.\r\n\r\nSelain menjadi ajang rekreasi, kegiatan ini juga menjadi wadah untuk memperkuat komunikasi dan solidaritas antar anggota. Dalam suasana santai dan penuh kebersamaan, setiap individu dapat membangun hubungan yang lebih solid, sehingga tercipta kerja sama yang semakin baik di dalam group. Family Gathering ini juga menjadi bentuk apresiasi atas kontribusi dan kebersamaan yang telah terjalin selama ini.\r\n\r\nKegiatan positif seperti Family Gathering memberikan banyak manfaat, di antaranya mempererat tali silaturahmi, meningkatkan rasa saling percaya, serta membangun kekompakan yang lebih kuat. Kebersamaan yang terjalin tidak hanya berdampak pada hubungan personal, tetapi juga pada semangat kolaborasi dalam setiap kegiatan group. Dengan adanya kegiatan seperti ini, Group Vertical Dreams semakin membuktikan bahwa kebersamaan adalah fondasi utama dalam meraih mimpi dan tujuan bersama.\r\n\r\nMelalui Family Gathering ini, diharapkan semangat kekeluargaan dan solidaritas dalam Group Vertical Dreams akan terus tumbuh dan berkembang, membawa energi positif untuk setiap langkah ke depan.', 'thumb_1775453222_69d344268225c.png', 2, 3, 'published', 17, '2026-04-06 05:06:38', '2026-04-06 07:32:41'),
(5, '6 Penyebab Pusing Setelah Olahraga dan Cara Mengatasinya', '6-penyebab-pusing-setelah-olahraga-dan-cara-mengatasinya', 'Pusing setelah olahraga umumnya bukan kondisi serius dan sering disebabkan oleh faktor seperti dehidrasi, paparan sinar matahari, teknik olahraga yang kurang tepat, kurangnya oksigen ke otak, penurunan gula darah, atau tekanan darah rendah. Kondisi ini bisa diatasi dengan cara sederhana seperti mencukupi cairan tubuh, beristirahat di tempat teduh, memperbaiki teknik dan pola napas, makan sebelum olahraga, serta melakukan pemanasan dan pendinginan. Meski biasanya tidak berbahaya, pusing yang berkepanjangan atau semakin parah sebaiknya diperiksakan ke dokter.', 'Pernahkah Anda merasa sakit kepala atau pusing setelah olahraga? Meskipun terkadang mengganggu dan menghambat aktivitas, namun kondisi ini sebetulnya tidak perlu Anda khawatirkan secara berlebih. Mungkin saja penyebab pusing setelah olahraga yang Anda alami merupakan tanda dehidrasi. Untuk mengatasi pusing penyebab dehidrasi, segeralah minum air putih yang cukup setelah berolahraga.\r\n\r\nNamun, bukan cuma karena dehidrasi, ada beberapa penyebab pusing setelah olahraga lain. Untuk selengkapnya, yuk simak ulasan mengenai berbagai penyebab dan cara mengatasi pusing setelah olahraga di bawah ini!\r\n\r\nPenyebab Pusing Setelah Olahraga & Cara Mengatasi\r\nKenapa habis olahraga pusing? penyebab pusing setelah olahraga biasanya bukanlah gejala dari suatu kondisi serius. Berikut beberapa faktor penyebab pusing yang Anda alami.\r\n\r\n1. Terlalu Lama Terpapar Sinar Matahari\r\nJika Anda berolahraga di luar ruangan, mungkin saja penyebab sakit kepala atau pusing setelah olahraga yang Anda alami ialah karena terlalu lama terkena paparan sinar matahari. Bahkan ketika tidak berolahraga sekalipun Anda bisa merasa sakit kepala jika berada di bawah teriknya matahari dalam jangka waktu lama.\r\n\r\nJika Anda sudah cukup lama terpapar sinar matahari, cobalah pindah ke lokasi teduh dan beristirahat sebentar. Anda sebaiknya membawa handuk dingin yang lembab untuk diletakkan di dahi dan atas mata selama beberapa menit. Atau, Anda juga bisa menggunakan kaca mata hitam dan topi ketika berolahraga di luar ruangan, supaya tidak terkena sinar langsung.\r\n\r\n2. Teknik Olahraga Kurang Tepat\r\nPenyebab pusing setelah olahraga lainnya adalah karena teknik olahraga yang kurang tepat. Ya, ketika Anda salah melakukan gerakan, Anda mungkin saja mengalami tegang otot pada leher dan bahu. Hal inilah yang kemudian menyebabkan sakit kepala tersebut.\r\n\r\nMaka dari itu, jika Anda masih pemula dalam olahraga tertentu. Cara menghilangkan pusing setelah olahraga bisa dengan meminta bantuan instruktur olahraga profesional untuk memandu Anda melakukan teknik olahraga secara tepat. Atau, Anda bisa menonton panduannya di berbagai video youtube. Lalu, jangan lupa lakukan pemanasan sebelum dan pendinginan setelah selesai berolahraga.\r\n\r\n3. Berkurangnya Aliran Oksigen ke Otak\r\nKliyengan atau kepala pusing setelah berolahraga sering kali terjadi ketika seseorang melakukan olahraga dengan intensitas tinggi atau angkat beban. Biasanya, Anda akan sulit mengambil napas atau bahkan tersengal-sengal. Bukan tanpa sebab, hal ini sebetulnya merupakan pertanda kurangnya oksigen di otak [1].\r\n\r\nPenggunaan kekuatan tubuh yang berlebihan akan mendorong jantung bekerja dengan keras. Akibatnya, suplai darah ke otak menjadi berkurang hingga menyebabkan sakit kepala. Padahal, tubuh memerlukan lebih banyak oksigen dibanding biasanya selama berolahraga.\r\n\r\nDi sisi lain, kondisi ini juga bisa dikarenakan teknik bernapas Anda kurang tepat. Maka dari itu, untuk cara menghilangkan pusing setelah olahraga penting untuk menyesuaikan pola napas. Sesuai kemampuan tubuh dan jenis olahraga yang dilakukan.\r\n\r\nMisalkan, ketika sedang jogging di pagi hari, Anda dapat menyelaraskan pola napas dengan langkah kaki. Caranya, ambil napas melalui hidung setiap empat langkah, lalu keluarkan lewat mulut.\r\n\r\nAtau, cobalah istirahat sejenak dan atur pola napas Anda selama beberapa menit. Tarik napas panjang dan hembuskan secara perlahan beberapa kali hingga akhirnya napas Anda terasa lebih baik. Sebaiknya, hindari olahraga berat jika belum terbiasa karena kapasitas paru Anda belum beradaptasi.\r\n\r\n4. Gula Darah Menurun\r\nSalah satu penyebab pusing setelah olahraga bisa jadi lantaran menurunnya kadar gula darah secara drastis. Pada kondisi ini, Anda biasanya akan mengalami gemetar, keringat dingin, atau lemas. Adapun penyebab gula darah turun tiba-tiba salah satunya ialah karena Anda berolahraga dalam keadaan perut kosong. \r\n\r\nBagaimana tidak, olahraga tanpa makan sebelumnya akan membuat tubuh kekurangan sumber energi utamanya yaitu glukosa menyebabkan pusing setelah olahraga. Oleh sebab itu, jangan heran saat Anda mendadak merasakan pusing disertai berkunang-kunang pasca berolahraga jika Anda belum makan. Namun, sebaiknya hindari mengonsumsi makanan berat karena berisiko menyebabkan abdominal discomfort (kram perut, mual, muntah).\r\n\r\nAdapun cara mengatasi pusing setelah olahraga akibat gula darah rendah ialah dengan mengonsumsi makanan tinggi protein dan karbohidrat setidaknya 2 jam sebelum pergi berolahraga. Hal ini akan membantu Anda memiliki energi sehingga kadar gula darah tetap terjaga dengan baik.\r\n\r\n5. Tekanan Darah Rendah\r\nPenyebab pusing setelah olahraga berikutnya ialah tekanan darah rendah. Ketika berolahraga, jantung bekerja lebih keras dan mempompa darah lebih banyak. Hal ini membuat pembuluh darah melebar guna menampung kelebihan darah yang ada.\r\n\r\nNamun, saat Anda selesai berolahraga dan jantung mulai kembali berdetak dengan normal, pembuluh darah memerlukan waktu lebih lama untuk menyesuaikannya. Akibatnya, Anda pun mengalami penurunan tekanan darah hingga membuat tubuh terasa lemas, pusing, dan sakit kepala.\r\n\r\nKondisi sebetulnya cukup wajar dan bisa Anda atasi dengan beristirahat sejenak. Atau, Anda bisa menaruh kaki anda lebih tinggi dari posisi jantung anda sambil tidur terlentang. Hal ini bertujuan untuk meningkatkan aliran darah balik ke jantung. Jangan lupa lakukan gerakan pendinginan setelah olahraga untuk membantu mengembalikan kondisi tubuh secara perlahan.\r\n\r\n6. Dehidrasi\r\nPenyebab pusing setelah olahraga yang paling umum terjadi ialah karena dampak dehidrasi, yaitu kondisi ketika tubuh Anda kekurangan cairan. Biasanya, saat Anda berolahraga, tubuh akan lebih banyak mengeluarkan keringat hingga membuatnya kehilangan cairan dalam jumlah besar. Hal ini juga sekaligus meningkatkan asupan cairan yang harus Anda penuhi.\r\n\r\nApabila tidak cukup minum, maka Anda pun berisiko mengalami dehidrasi yang kemudian menyebabkan pusing atau sakit kepala. Di samping itu, air juga memegang peran krusial dalam mempertahankan fungsi otak dan organ-organ lainnya.\r\n\r\nNah, mengingat pentingnya minum air putih, pastikan Anda selalu sedia #AQUADULU di rumah agar kebutuhan cairan keluarga tetap terpenuhi. AQUA menawarkan air mineral galon terbaik yang bisa Anda dapatkan dengan harga terjangkau. Kemasan air Aqua galon 19 liter bahkan dirancang khusus agar menjadi ramah lingkungan dan aman digunakan.\r\n\r\nApabila Anda ingin berolahraga di luar, AQUA juga tersedia dalam berbagai kemasan yang mudah dibawa dan cocok untuk menemani aktivitas Anda. Bahkan, AQUA juga memiliki kemasan Click & Go 750ml dengan sports cap yang memudahkan Anda untuk minum air saat berolahraga.\r\n\r\nDalam proses pengemasannya, AQUA menerapkan sistem integrasi menyeluruh melalui 400 tahap uji kualitas sebelum akhirnya sampai ke tangan Anda. Sehingga, kualitas dan kemurniannya tak perlu Anda ragukan lagi.\r\n\r\nDemikianlah ulasan mengenai penyebab pusing setelah olahraga dan tips mengatasinya. Meskipun umumnya kondisi tersebut bukan hal berbahaya, namun tidak menutup kemungkinan pula jika Anda mengalami masalah medis tertentu. Anda sebaiknya segera memeriksakan diri ke dokter apabila gejala pusing dan sakit kepala tak kunjung mereda atau bahkan semakin parah.', 'thumb_1775462507_69d3686be23e9.webp', 4, 1, 'published', 0, '2026-04-06 08:01:47', '2026-04-06 08:01:47'),
(6, 'Perkembangan Teknologi Pada Era Digital', 'perkembangan-teknologi-pada-era-digital', 'Perkembangan teknologi digital yang pesat membawa perubahan besar dalam kehidupan manusia, mulai dari cara belajar, bekerja, hingga berbisnis. Inovasi seperti kecerdasan buatan, 5G, IoT, hingga realitas virtual membuat aktivitas menjadi lebih mudah, cepat, dan efisien. Meski memiliki tantangan, teknologi tetap menjadi bagian penting yang tidak terpisahkan dari kehidupan modern dan akan terus berkembang di masa depan.', 'Belakangan ini teknologi terus berkembang dengan sangat pesat. Teknologi membawa inovasi yang mengubah cara kita hidup, bekerja, dan berkomunikasi . Era digital saat ini menjadi saksi perkembangan teknologi terbaru yang membawa dampak yang sangat signifikan dalam berbagai aspek kehidupan. Banyak sekali orang didunia ini yang memanfaatkan perkembangan ini seperti sekolah-sekolah yang sudah mulai menggunakan laptop dalam kegiatan ajar-mengajar setiap harinya.\r\n\r\nTeknologi terbaru masa kini membawa kita menuju era transformasi digital yang memukau. Dari kemajuan dalam kecerdasan buatan yang memungkinkan mesin untuk belajar dan beradaptasi, hingga jaringan 5G yang menghadirkan konektivitas supercepat dan dapat diandalkan, inovasi ini mempengaruhi setiap aspek kehidupan kita. Internet of Things (IoT) memungkinkan perangkat untuk berkomunikasi dan berbagi data secara langsung, menciptakan lingkungan yang terhubung dan efisien. Selain itu, teknologi biometrik, seperti pengenalan wajah dan sidik jari, telah memperkuat lapisan keamanan di berbagai sektor. Sementara itu, realitas virtual (VR) dan augmented reality (AR) memberikan pengalaman yang mendalam dan interaktif, merubah cara kita belajar, bermain, dan bekerja. Kemudian ada teknologi yang bernama Quantum computing yaitu teknologi komputasi yang berpotensi menghadirkan komputer yang sangat kuat, mampu menyelesaikan masalah yang sulit sekalipun dengan hitungan detik. Dalam bidang sains, kesehatan, dan keuangan, quantum computing dapat mengakselerasi penemuan dan inovasi.\r\n\r\nDengan kita menerapkan teknologi dalam kehidupan, dapat mempermudah kita sebagai manusia untuk melakukan aktivitas setiap harinya. Teknologi tidak akan pernah mati sampai kapanpun. Teknologi terus berkembang setiap hari, setiap bulan, setiap tahun, setiap saat. Segala sesuatu pada jaman sekarang pasti akan melibatkan yang namanya teknologi terumata dalam bidang digital. Teknologi mempunyai kelebihan dan kekurangannya masing-masing. Manfaat teknologi ini akan sangat bermanfaat bagi banyak orang, diantaranya adalah mengembangkan lapangan kerja baru, memperlancar komunikasi, mempermudah proses jual beli, memperluas wawasan,\r\n\r\nsumber informasi, hiburan, dan masih banyak lagi hal yang bisa dikembangkan oleh teknologi. Kita bisa lihat bermacam-macam teknologi yang telah tersebar, antara lain dalm bidang teknologi transportasi, teknologi komunikasi, teknologi informasi, teknologi kedokteran, teknologi pendidikan, dan teknik sipil.\r\n\r\nPada ujungnya kita semua akan terpengaruh dengan teknologi yang akan mendatang, karena teknologi digital adalah teknologi yang sangat melekat kepada manusia. Teknologi digital sangat berpengaruh besar bagi kita Masyarakat. Dapat dimanfaatkan sebagai sarana perdagangan yang menjadi salah satu kunci kehidupan. Di era digital ini akan sangat mudah untuk melakukan kegiatan jual beli, sudah banyak toko online yang menyebar pada platform yang terpercaya. Bisnis pada era digital sudah sangat berkembang dari jaman ke jaman, kita dapat mempromosikan produk kita dengan gampang dan tidak perlu membayar dengan harga yang sangat tinggi. Dengan teknologi ini pun transaksi jual beli menjadi sangat mudah yaitu kita dapat menggukan uang elektronik, yang bisa dilakukan kapan saja dan Dimana pun kita berada.\r\n\r\nSemua inovasi ini bersama-sama membentuk lanskap teknologi yang dinamis dan menjanjikan, mempersiapkan kita untuk tantangan masa depan yang semakin kompleks. Dapat disimpulkan bahwa kita sebagai manusia tidak bisa terlepas dengan yang Namanya teknologi terutama dalam bidang teknologi digital. Teknologi digital sangat banyak telah mempengaruhi kita semua, yang menimbulkan sifat ketergantungan di masa depan. Teknologi akan terus berkembang sampai kapanpun karena teknologi akan membantu pekerjaan manusia menjadi lebih efisien dan lebih praktis.', 'thumb_1775469245_69d382bd0e6fe.jpg', 1, 4, 'published', 1, '2026-04-06 09:54:05', '2026-04-06 09:54:12'),
(7, 'Lifestyle Sehat: Kunci Hidup Lebih Bahagia dan Produktif', 'lifestyle-sehat-kunci-hidup-lebih-bahagia-dan-produktif', 'Gaya hidup sehat bukan sekadar tren, tetapi kebutuhan penting untuk menjaga tubuh dan pikiran tetap optimal di tengah aktivitas modern.', 'Di era modern yang serba cepat, menjaga keseimbangan antara gaya hidup dan kesehatan menjadi hal yang sangat penting. Banyak orang terjebak dalam rutinitas padat, pola makan tidak teratur, serta kurangnya aktivitas fisik, yang pada akhirnya berdampak buruk bagi kesehatan tubuh dan mental. Oleh karena itu, menerapkan lifestyle sehat bukan hanya pilihan, melainkan kebutuhan untuk menunjang kualitas hidup yang lebih baik.\r\n\r\nSalah satu kunci utama dalam menjalani gaya hidup sehat adalah menjaga pola makan. Konsumsi makanan bergizi seimbang yang mengandung karbohidrat, protein, lemak sehat, vitamin, dan mineral sangat penting untuk memenuhi kebutuhan tubuh. Mengurangi makanan cepat saji serta memperbanyak konsumsi sayur dan buah dapat membantu meningkatkan daya tahan tubuh dan menjaga berat badan tetap ideal.\r\n\r\nSelain itu, aktivitas fisik juga memegang peranan penting. Tidak perlu olahraga berat, cukup dengan berjalan kaki, jogging ringan, atau bersepeda secara rutin sudah dapat memberikan manfaat besar bagi kesehatan jantung, otot, dan kebugaran tubuh secara keseluruhan. Olahraga juga terbukti mampu mengurangi stres dan meningkatkan suasana hati.\r\n\r\nKesehatan mental pun tidak kalah penting untuk diperhatikan. Di tengah tekanan pekerjaan dan kehidupan sosial, penting untuk memberikan waktu bagi diri sendiri untuk beristirahat dan melakukan hal-hal yang disukai. Meditasi, membaca, atau sekadar menikmati waktu santai dapat membantu menjaga keseimbangan emosi dan pikiran.\r\n\r\nIstirahat yang cukup juga menjadi bagian penting dari lifestyle sehat. Tidur selama 7–8 jam per malam membantu tubuh melakukan regenerasi sel dan menjaga fungsi organ tetap optimal. Kurang tidur dapat menyebabkan berbagai masalah kesehatan, seperti menurunnya konsentrasi hingga gangguan sistem imun.\r\n\r\nPada akhirnya, menerapkan gaya hidup sehat tidak harus dilakukan secara drastis. Mulailah dari kebiasaan kecil seperti minum air putih yang cukup, bergerak lebih aktif, dan mengatur waktu istirahat. Dengan konsistensi, perubahan kecil tersebut akan memberikan dampak besar bagi kesehatan dan kualitas hidup Anda di masa depan.', 'thumb_1775469740_69d384ac1af31.jpg', 2, 5, 'published', 0, '2026-04-06 10:02:20', '2026-04-06 10:02:20'),
(8, 'Strategi Cerdas Mengelola Bisnis dan Keuangan di Era Modern', 'strategi-cerdas-mengelola-bisnis-dan-keuangan-di-era-modern', 'Mengelola bisnis dan keuangan dengan baik menjadi kunci utama untuk mencapai kestabilan dan kesuksesan di tengah persaingan yang semakin ketat.', 'Di era modern saat ini, bisnis dan keuangan menjadi dua hal yang saling berkaitan dan tidak bisa dipisahkan. Perkembangan teknologi serta perubahan perilaku konsumen menuntut para pelaku usaha untuk lebih adaptif dan cerdas dalam mengelola strategi bisnis sekaligus keuangan mereka. Tanpa pengelolaan yang baik, bisnis yang potensial sekalipun dapat mengalami kesulitan dalam berkembang.\r\n\r\nSalah satu langkah penting dalam menjalankan bisnis adalah memiliki perencanaan yang matang. Perencanaan ini mencakup penentuan target pasar, strategi pemasaran, hingga pengelolaan operasional. Dengan perencanaan yang jelas, pelaku usaha dapat meminimalisir risiko serta memaksimalkan peluang yang ada. Selain itu, inovasi juga menjadi faktor penting agar bisnis tetap relevan dan mampu bersaing di pasar.\r\n\r\nDalam aspek keuangan, pencatatan yang rapi dan terstruktur sangat diperlukan. Memisahkan keuangan pribadi dan bisnis adalah langkah dasar yang sering diabaikan, padahal hal ini sangat penting untuk mengetahui kondisi keuangan usaha secara nyata. Dengan laporan keuangan yang jelas, pemilik bisnis dapat mengambil keputusan yang lebih tepat, seperti menentukan harga, mengontrol pengeluaran, hingga merencanakan investasi.\r\n\r\nManajemen arus kas (cash flow) juga menjadi hal krusial dalam keberlangsungan bisnis. Banyak usaha yang gagal bukan karena tidak menghasilkan keuntungan, tetapi karena arus kas yang tidak sehat. Oleh karena itu, penting untuk memastikan pemasukan dan pengeluaran tetap seimbang serta memiliki cadangan dana untuk menghadapi situasi darurat.\r\n\r\nDi sisi lain, pemanfaatan teknologi digital dapat membantu mempermudah pengelolaan bisnis dan keuangan. Berbagai platform digital kini tersedia untuk mendukung pemasaran, transaksi, hingga pencatatan keuangan secara otomatis dan efisien. Hal ini memungkinkan pelaku usaha untuk lebih fokus pada pengembangan bisnis.\r\n\r\nKesimpulannya, kesuksesan dalam bisnis tidak hanya ditentukan oleh ide yang bagus, tetapi juga oleh kemampuan dalam mengelola keuangan secara bijak. Dengan perencanaan yang tepat, pengelolaan keuangan yang disiplin, serta pemanfaatan teknologi, bisnis dapat tumbuh secara berkelanjutan dan menghadapi tantangan di masa depan dengan lebih siap.', 'thumb_1775471102_69d389fe5c2d2.jpg', 3, 6, 'published', 0, '2026-04-06 10:25:02', '2026-04-06 10:25:02'),
(9, 'Pentingnya Pendidikan dalam Membangun Masa Depan', 'pentingnya-pendidikan-dalam-membangun-masa-depan', 'Pendidikan menjadi fondasi utama dalam membentuk karakter, keterampilan, dan kualitas sumber daya manusia di era modern.', 'Pendidikan merupakan salah satu aspek terpenting dalam kehidupan manusia. Melalui pendidikan, seseorang tidak hanya memperoleh pengetahuan, tetapi juga membentuk karakter, pola pikir, serta keterampilan yang dibutuhkan untuk menghadapi tantangan di masa depan. Di era globalisasi dan digital saat ini, pendidikan memiliki peran yang semakin krusial dalam menciptakan generasi yang unggul dan berdaya saing tinggi.\r\n\r\nSeiring perkembangan zaman, sistem pendidikan juga mengalami banyak perubahan. Teknologi telah membawa transformasi besar dalam dunia pendidikan, mulai dari metode pembelajaran hingga akses informasi. Kini, proses belajar tidak lagi terbatas pada ruang kelas, melainkan dapat dilakukan secara daring melalui berbagai platform digital. Hal ini memberikan kemudahan bagi siapa saja untuk belajar kapan pun dan di mana pun.\r\n\r\nSelain pengetahuan akademik, pendidikan juga berperan dalam mengembangkan soft skills seperti kemampuan komunikasi, kerja sama, berpikir kritis, dan kreativitas. Keterampilan ini sangat dibutuhkan di dunia kerja maupun dalam kehidupan sehari-hari. Oleh karena itu, pendidikan tidak hanya berfokus pada nilai, tetapi juga pada pembentukan kepribadian yang baik.\r\n\r\nPeran guru dan lingkungan juga sangat penting dalam proses pendidikan. Guru tidak hanya sebagai pengajar, tetapi juga sebagai pembimbing dan motivator bagi peserta didik. Sementara itu, lingkungan yang positif akan mendukung perkembangan belajar yang optimal.\r\n\r\nNamun, masih terdapat berbagai tantangan dalam dunia pendidikan, seperti kesenjangan akses, kualitas fasilitas, hingga kurangnya pemerataan teknologi. Oleh karena itu, diperlukan kerja sama antara pemerintah, institusi pendidikan, dan masyarakat untuk menciptakan sistem pendidikan yang lebih baik dan merata.\r\n\r\nKesimpulannya, pendidikan adalah investasi jangka panjang yang sangat berharga. Dengan pendidikan yang baik, seseorang dapat meningkatkan kualitas hidupnya serta berkontribusi dalam pembangunan masyarakat dan negara. Oleh karena itu, penting bagi setiap individu untuk terus belajar dan mengembangkan diri sepanjang hayat.', 'thumb_1775471283_69d38ab38667b.jpg', 5, 3, 'published', 1, '2026-04-06 10:28:03', '2026-04-06 10:28:22');

-- --------------------------------------------------------

--
-- Table structure for table `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `article_tags`
--

INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 5),
(3, 5),
(1, 6),
(2, 7),
(4, 7),
(3, 8),
(8, 9),
(8, 14),
(7, 19),
(7, 20),
(7, 26),
(7, 27),
(9, 29),
(9, 31),
(9, 37);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Teknologi', 'teknologi', 'Artikel seputar dunia teknologi terkini', '2026-04-06 04:44:34'),
(2, 'Lifestyle', 'lifestyle', 'Tips dan gaya hidup modern', '2026-04-06 04:44:34'),
(3, 'Bisnis', 'bisnis', 'Dunia bisnis dan entrepreneurship', '2026-04-06 04:44:34'),
(4, 'Kesehatan', 'kesehatan', 'Informasi kesehatan dan wellness', '2026-04-06 04:44:34'),
(5, 'Pendidikan', 'pendidikan', 'Artikel seputar pendidikan dan pengembangan diri', '2026-04-06 04:44:34');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `article_id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','spam') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `article_id`, `parent_id`, `name`, `email`, `content`, `status`, `created_at`) VALUES
(1, 1, NULL, 'Budi Santoso', 'budi@email.com', 'Artikel yang sangat bermanfaat! Saya sudah mencoba tutorial ini dan berhasil.', 'approved', '2026-04-06 04:44:34'),
(2, 1, NULL, 'Sari Dewi', 'sari@email.com', 'Terima kasih tutorialnya, sangat jelas dan mudah dipahami untuk pemula seperti saya.', 'approved', '2026-04-06 04:44:34'),
(3, 2, NULL, 'Ahmad Fauzi', 'ahmad@email.com', 'Tips yang sangat berguna! Saya sudah menerapkan teknik Pomodoro dan terasa jauh lebih produktif.', 'approved', '2026-04-06 04:44:34'),
(4, 3, NULL, 'Rina Kusuma', 'rina@email.com', 'Artikel yang inspiratif! Saya sedang mempertimbangkan untuk memulai bisnis dropshipping.', 'approved', '2026-04-06 04:44:34'),
(5, 4, NULL, 'kangmas', 'tamamsantuy15@gmail.com', 'Abang jaket pink ganteng banget ya🤭', 'approved', '2026-04-06 05:29:46'),
(6, 4, NULL, 'kangmas', 'tamamsantuy15@gmail.com', 'Abang jaket pink ganteng banget ya🤭', 'approved', '2026-04-06 05:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'PHP', 'php', '2026-04-06 04:44:34'),
(2, 'MySQL', 'mysql', '2026-04-06 04:44:34'),
(3, 'Web Development', 'web-development', '2026-04-06 04:44:34'),
(4, 'JavaScript', 'javascript', '2026-04-06 04:44:34'),
(5, 'Tips', 'tips', '2026-04-06 04:44:34'),
(6, 'Tutorial', 'tutorial', '2026-04-06 04:44:34'),
(7, 'Produktivitas', 'produktivitas', '2026-04-06 04:44:34'),
(8, 'Startup', 'startup', '2026-04-06 04:44:34'),
(9, 'Bisnis Online', 'bisnis-online', '2026-04-06 09:58:07'),
(10, 'Entrepreneurship', 'entrepreneurship', '2026-04-06 09:58:07'),
(11, 'Marketing Digital', 'marketing-digital', '2026-04-06 09:58:07'),
(12, 'Investasi', 'investasi', '2026-04-06 09:58:07'),
(13, 'Keuangan Pribadi', 'keuangan-pribadi', '2026-04-06 09:58:07'),
(14, 'E-Commerce', 'e-commerce', '2026-04-06 09:58:07'),
(15, 'UMKM', 'umkm', '2026-04-06 09:58:07'),
(16, 'Karir', 'karir', '2026-04-06 09:58:07'),
(17, 'Freelance', 'freelance', '2026-04-06 09:58:07'),
(18, 'Passive Income', 'passive-income', '2026-04-06 09:58:07'),
(19, 'Kesehatan Mental', 'kesehatan-mental', '2026-04-06 09:58:07'),
(20, 'Olahraga', 'olahraga', '2026-04-06 09:58:07'),
(21, 'Nutrisi', 'nutrisi', '2026-04-06 09:58:07'),
(22, 'Diet Sehat', 'diet-sehat', '2026-04-06 09:58:07'),
(23, 'Meditasi', 'meditasi', '2026-04-06 09:58:07'),
(24, 'Tidur Berkualitas', 'tidur-berkualitas', '2026-04-06 09:58:07'),
(25, 'Kesehatan Wanita', 'kesehatan-wanita', '2026-04-06 09:58:07'),
(26, 'Vitamin & Suplemen', 'vitamin-suplemen', '2026-04-06 09:58:07'),
(27, 'Kebugaran', 'kebugaran', '2026-04-06 09:58:07'),
(28, 'Mindfulness', 'mindfulness', '2026-04-06 09:58:07'),
(29, 'Belajar Online', 'belajar-online', '2026-04-06 09:58:07'),
(30, 'Beasiswa', 'beasiswa', '2026-04-06 09:58:07'),
(31, 'Pengembangan Diri', 'pengembangan-diri', '2026-04-06 09:58:07'),
(32, 'Bahasa Asing', 'bahasa-asing', '2026-04-06 09:58:07'),
(33, 'Keterampilan Baru', 'keterampilan-baru', '2026-04-06 09:58:07'),
(34, 'Parenting', 'parenting', '2026-04-06 09:58:07'),
(35, 'Literasi Digital', 'literasi-digital', '2026-04-06 09:58:07'),
(36, 'Kurikulum', 'kurikulum', '2026-04-06 09:58:07'),
(37, 'Motivasi Belajar', 'motivasi-belajar', '2026-04-06 09:58:07'),
(38, 'E-Learning', 'e-learning', '2026-04-06 09:58:07'),
(39, 'Kecerdasan Buatan', 'kecerdasan-buatan', '2026-04-06 09:58:07'),
(40, 'Smartphone', 'smartphone', '2026-04-06 09:58:07'),
(41, 'Cybersecurity', 'cybersecurity', '2026-04-06 09:58:07'),
(42, 'Cloud Computing', 'cloud-computing', '2026-04-06 09:58:07'),
(43, 'Blockchain', 'blockchain', '2026-04-06 09:58:07'),
(44, 'Internet of Things', 'internet-of-things', '2026-04-06 09:58:07'),
(45, 'Data Science', 'data-science', '2026-04-06 09:58:07'),
(46, 'Open Source', 'open-source', '2026-04-06 09:58:07'),
(47, 'Gaming', 'gaming', '2026-04-06 09:58:07'),
(48, 'Gadget', 'gadget', '2026-04-06 09:58:07'),
(49, 'Travel', 'travel', '2026-04-06 09:58:07'),
(50, 'Kuliner', 'kuliner', '2026-04-06 09:58:07'),
(51, 'Fashion', 'fashion', '2026-04-06 09:58:07'),
(52, 'Minimalis', 'minimalis', '2026-04-06 09:58:07'),
(53, 'Hobi', 'hobi', '2026-04-06 09:58:07'),
(54, 'Hubungan', 'hubungan', '2026-04-06 09:58:07'),
(55, 'Keluarga', 'keluarga', '2026-04-06 09:58:07'),
(56, 'Dekorasi Rumah', 'dekorasi-rumah', '2026-04-06 09:58:07'),
(57, 'Lingkungan Hidup', 'lingkungan-hidup', '2026-04-06 09:58:07'),
(58, 'Self-Care', 'self-care', '2026-04-06 09:58:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','author') DEFAULT 'author',
  `status` enum('pending','active','suspended') NOT NULL DEFAULT 'active',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text,
  `avatar_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `avatar`, `bio`, `avatar_path`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@blog.com', '$2y$10$obtTJ9Qt/8Yq2ekW8stMceF3Yr7P3Gb24hT1JD4/y5u8n3euINSMa', 'Administrator', 'admin', 'active', NULL, 'Pengelola utama platform blog ini.', 'avatars/avatar_1_1775460601.jpg', '2026-04-06 04:44:34', '2026-04-06 07:30:47'),
(2, 'johndoe', 'john@blog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'author', 'active', NULL, 'Penulis teknologi dan sains.', NULL, '2026-04-06 04:44:34', '2026-04-06 04:44:34'),
(3, 'tamkha', 'elkhaa151105@gmail.com', '$2y$10$D1CyAoFCJX6g7XLd6dRkKuvLj1zAPALYIVAwibttnwrFkZCl0Ju.G', 'Khaerul Tamam', 'author', 'active', NULL, 'Prompter Muda Berambisi', NULL, '2026-04-06 04:56:37', '2026-04-06 04:57:04'),
(4, 'budi_santoso', 'budi@dinamisblog.com', '$2y$10$u6n3Uts5/rVW5EDMkB2mX.uP8DCxOge3o0Xck2cqY5DgYQk0Dzt/C', 'Budi Santoso', 'author', 'active', NULL, 'Penulis teknologi dan penggemar open source. Sudah 5 tahun berkecimpung di dunia web development dan senang berbagi ilmu melalui tulisan.', NULL, '2026-04-06 09:43:43', '2026-04-06 09:43:43'),
(5, 'sari_dewi', 'sari@dinamisblog.com', '$2y$10$ruhwBVsoIhooP4lAJPoITO6PaYqe/t2r1ZuMLQcu009g042sMoYYy', 'Sari Dewi', 'author', 'active', NULL, 'Content writer dan lifestyle blogger. Passionate tentang kesehatan mental, produktivitas, dan gaya hidup sehat. Ibu dua anak yang juga aktif menulis.', NULL, '2026-04-06 09:43:43', '2026-04-06 09:43:43'),
(6, 'rizky_pratama', 'rizky@dinamisblog.com', '$2y$10$6JPLiJsYhB0aP0.UQHb.SeNVFsT5BOGgU02JH85jkArnVbwc9/7B6', 'Rizky Pratama', 'author', 'active', NULL, 'Entrepreneur dan business coach. Mendirikan 3 startup di bidang edtech dan fintech. Menulis tentang bisnis, keuangan, dan pengembangan diri.', NULL, '2026-04-06 09:43:43', '2026-04-06 09:43:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
