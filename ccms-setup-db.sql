-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 06, 2022 at 04:54 PM
-- Server version: 5.7.37
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `iconicde_www`
--

-- --------------------------------------------------------

--
-- Table structure for table `ccms_blacklist`
--

CREATE TABLE `ccms_blacklist` (
  `id` int(11) NOT NULL,
  `data` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ccms_blacklist`
--

INSERT INTO `ccms_blacklist` (`id`, `data`) VALUES
(1, ''),
(2, 'viagra|v.i.a.g.r.a|v-i-a-g-r-a|cialis|c.i.a.l.i.s|c-i-a-l-i-s');

-- --------------------------------------------------------

--
-- Table structure for table `ccms_cache`
--

CREATE TABLE `ccms_cache` (
  `id` int(11) NOT NULL,
  `url` varchar(512) NOT NULL,
  `date` int(20) NOT NULL,
  `exp` int(20) NOT NULL,
  `content` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ccms_headers`
--

CREATE TABLE `ccms_headers` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `name` varchar(191) NOT NULL,
  `value` text NOT NULL,
  `note` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ccms_headers`
--

INSERT INTO `ccms_headers` (`id`, `status`, `name`, `value`, `note`) VALUES
(1, 1, 'Permissions-Policy', 'accelerometer=(), autoplay=(), camera=(), encrypted-media=(), fullscreen=(self), geolocation=(self), gyroscope=(), magnetometer=(), microphone=(), midi=(), payment=(self), picture-in-picture=(), sync-xhr=(), usb=()', 'Permissions-Policy (Feature-Policy)<br>\nControl browser’s features such as geolocation, fullscreen, speaker, USB, autoplay, speaker, vibrate, microphone, payment, vr, etc. to enable or disable within a web application.<br>\nhttps://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy<br>\nhttps://github.com/w3c/webappsec-permissions-policy/blob/master/permissions-policy-explainer.md#appendix-big-changes-since-this-was-called-feature-policy\n'),
(2, 1, 'X-Powered-By', '', 'Disable your PHP version number from showing up in HTTP headers for added security.'),
(3, 1, 'X-Frame-Options', 'sameorigin', 'Don\'t allow any pages to be framed - Defends against CSRF.'),
(4, 1, 'X-XSS-Protection', '1; mode=block', 'Turn on IE8-IE9 XSS prevention tools.'),
(5, 1, 'X-Content-Type-Options', 'nosniff', 'Prevent mime-based attacks.'),
(6, 1, 'X-UA-Compatible', 'IE=Edge', 'Use this to force IE to hide that annoying browser compatibility button in the address bar.<br>\nIE=edge means IE should use the latest (edge) version of its rendering engine.'),
(7, 1, 'Strict-Transport-Security', 'max-age=31536000; includeSubDomains', 'HSTS (HTTP Strict Transport Security) header to ensure all communication from a browser is sent over HTTPS (HTTP Secure).'),
(8, 1, 'Referrer-Policy', 'strict-origin-when-cross-origin', 'Setting the referrer to \'strict-origin-when-cross-origin\' means, requests for resource hosted somewhere else, like a Content Delivery Network (CDN), don\'t include anything else in the URI other than the protocol and the domain name. ie: https://example.com'),
(9, 1, 'Expect-CT', 'enforce, max-age=43200', 'A new header still in experimental status is to instruct the browser to validate the connection with web servers for certificate transparency (CT). This project aims to fix some of the flaws in the SSL/TLS certificate system.<br>\nhttps://certificate.transparency.dev/<br>\nhttps://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expect-CT<br>\nhttps://docs.report-uri.com/setup/ect/'),
(10, 1, 'Content-Security-Policy', 'base-uri \'none\'; connect-src \'self\' https: *.cloudfront.net *.doubleclick.net *.google.com *.googleapis.com *.googletagmanager.com *.google-analytics.com *.gstatic.com; form-action \'self\'; frame-ancestors \'self\'; img-src \'self\' data: https: *.cloudfront.net *.doubleclick.net *.gstatic.com *.google-analytics.com *.googleapis.com *.googleusercontent.com *.googletagmanager.com *.google.com *.gravatar.com; object-src \'none\'; worker-src \'self\'; script-src \'self\' https: \'nonce-{NONCE}\' \'strict-dynamic\' \'unsafe-inline\'{UNSAFE-EVAL}; script-src-attr \'nonce-{NONCE}\' \'strict-dynamic\';', '');

-- --------------------------------------------------------

--
-- Table structure for table `ccms_ins_db`
--

CREATE TABLE `ccms_ins_db` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `access` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=www side 1=admin side',
  `grp` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `en` text NOT NULL,
  `es` text NOT NULL,
  `ko` text NOT NULL,
  `ko-kp` text NOT NULL,
  `ko-kr` text NOT NULL,
  `vi` text NOT NULL,
  `zh-cn` text NOT NULL,
  `zh-tw` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ccms_ins_db`
--

INSERT INTO `ccms_ins_db` (`id`, `status`, `access`, `grp`, `name`, `en`, `es`, `ko`, `ko-kp`, `ko-kr`, `vi`, `zh-cn`, `zh-tw`) VALUES
(18, 1, 0, 'all', 'company-name', 'ICONIC Dentistry', '', '', '', '', '', '', ''),
(12, 1, 0, 'all', 'login2', 'Logged in as', 'Conectado como', '로그인 상태', '', '', '', '登录为', '登錄為'),
(13, 1, 0, 'all', 'login3', 'Logout', 'Cerrar sesión', '로그 아웃', '', '', '', '登出', '註銷'),
(14, 1, 0, 'all', 'login1', 'Login', 'Iniciar sesión', '로그인', '', '', '', '登录', '註冊'),
(16, 1, 0, 'all', 'cookie-button', 'Continue', '', '잇다', '', '', '', '继续', ''),
(17, 1, 0, 'all', 'cookie-text', 'This website uses cookies to manage language preferences, session persistence and help provide you with the best possible experience during your visit.', 'Este sitio web utiliza cookies para gestionar las preferencias de idioma, la persistencia de las sesiones y ayudarle a proporcionarle la mejor experiencia posible durante su visita.', '이 웹 사이트는 쿠키를 사용하여 언어 기본 설정, 세션 지속성을 관리하고 방문하는 동안 최고의 경험을 제공 할 수 있도록 도와줍니다.', '', '', '', '本网站使用cookies来管理语言偏好，会话持续性，并帮助您在访问期间提供最好的体验。', ''),
(19, 1, 0, 'all', 'about-us', 'About Us', 'Sobre Nosotros', '회사 소개', '', '', 'Về chúng tôi', '关于我们', ''),
(28, 1, 0, 'all', 'go', 'Go', 'Ir', '가기', '', '', 'Đi', '走', ''),
(29, 1, 0, 'all', 'phone', 'Phone', 'Teléfono', '전화', '', '', 'Điện thoại', '电话', ''),
(21, 1, 0, 'all', 'contact-us', 'Contact Us', 'Contáctenos', '연락처', '', '', 'Liên hệ chúng tôi', '联系我们', ''),
(27, 1, 0, 'all', 'search', 'Search', 'Buscar', '수색', '', '', 'Tìm kiếm', '搜索', ''),
(22, 1, 0, 'all', 'homepage', 'Homepage', 'Casa', '집', '', '', 'Nhà', '家', ''),
(40, 1, 0, 'all', 'email-placeholder', 'Email Address', 'Dirección de correo electrónico', '이메일 주소', '', '', 'Địa chỉ email', '电子邮箱', ''),
(23, 1, 0, 'all', 'copyright', 'Copyright &copy; {CCMS_LIB:_default.php;FUNC:ccms_dateYear} <a href=\"//{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}\">ICONIC Dentistry</a>. All Rights Reserved.', 'Derechos de autor &copy; {CCMS_LIB:_default.php;FUNC:ccms_dateYear} <a href=\"//{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}\">ICONIC Dentistry</a>. Todos los derechos reservados.', '저작권 &copy; {CCMS_LIB:_default.php;FUNC:ccms_dateYear} <a href=\"//{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}\">ICONIC Dentistry</a>. 판권 소유.', '', '', 'Bản quyền &copy; {CCMS_LIB:_default.php;FUNC:ccms_dateYear} <a href=\"//{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}\">ICONIC Dentistry</a>. Đã đăng ký Bản quyền.', '版权所有 &copy; {CCMS_LIB:_default.php;FUNC:ccms_dateYear} <a href=\"//{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}\">ICONIC Dentistry</a>。 版权所有。', ''),
(24, 1, 0, 'index', 'welcome', 'Welcome to ICONIC Dentistry', 'Bienvenidos a Icondds', 'Icondds에 오신 것을 환영합니다.', '', '', 'Chào mừng đến với Icondds', '欢迎来到Icondds', ''),
(30, 1, 0, 'all', 'address', 'Address', 'Dirección', '주소', '', '', 'Địa chỉ nhà', '地址', ''),
(31, 1, 0, 'all', 'hours', 'Hours', 'Horas', '시간', '', '', 'Giờ', '小时', ''),
(32, 1, 0, 'all', 'fax', 'Fax', 'Fax', '팩스', '', '', 'Số fax', '传真', ''),
(33, 1, 0, 'all', 'email', 'Email', 'Email', '이메일', '', '', 'E-mail', '电子邮件', ''),
(34, 1, 0, 'all', 'search-text-01', 'Enter keywords or phrases below to search.', 'Introduzca palabras clave o frases a continuación para buscar.', '검색 할 키워드 또는 구문을 입력하십시오.', '', '', 'Nhập từ khóa hoặc cụm từ dưới đây để tìm kiếm.', '输入以下关键字或短语进行搜索。', ''),
(26, 1, 0, 'index', 'description', 'Our office at ICONIC Dentistry on Irvine Blvd, Tustin, CA offers everything from high-quality dental implants to routine checkups. Call (714) 835-4441 today!', '', '', '', '', '', '', ''),
(35, 1, 0, 'all', 'contact-us-text-01', 'If you have any comments, questions or would like to speak with a real person, please contact us via:', 'Si tiene algún comentario, pregunta o le gustaría hablar con una persona real, contáctenos a través de:', '의견이나 질문이 있거나 실제 사람과 이야기하고 싶다면 다음을 통해 문의하십시오 :', '', '', 'Nếu bạn có bất kỳ ý kiến, câu hỏi hoặc muốn nói chuyện với một người thực sự, xin vui lòng liên hệ với chúng tôi qua:', '如果您有任何意见，问题或希望与真人交谈，请通过以下方式联系我们：', ''),
(36, 1, 0, 'all', 'privacy-policy', 'Privacy Policy', 'Política de privacidad', '개인 정보 정책', '', '', 'Chính sách bảo mật', '隐私政策', ''),
(37, 1, 0, 'all', 'sitemap', 'Sitemap', 'Mapa del sitio', '사이트 맵', '', '', 'Sơ đồ trang web', '网站地图', ''),
(38, 1, 0, 'all', 'quick-site-nav', 'Quick Site Nav', 'Navegación rápida del sitio', '빠른 사이트 탐색', '', '', 'Trang web nhanh', '快速站点导航', ''),
(39, 1, 0, 'all', 'name', 'Name', 'Nombre', '성명', '', '', 'Tên', '名字', ''),
(41, 1, 0, 'all', 'message', 'Message', 'Mensaje', '내용', '', '', 'Thông điệp', '消息', ''),
(47, 1, 0, 'all', 'search-placeholder-01', 'Search...', 'Buscar...', '검색...', '', '', 'Tìm kiếm...', '搜索...', ''),
(43, 1, 0, 'all', 'name-placeholder', 'Full Name', 'Nombre completo', '성명', '', '', 'Họ và tên', '全名', ''),
(49, 1, 0, 'all', 'cancel', 'Cancel', 'Cancelar', '취소', '', '', 'Hủy bỏ', '取消', ''),
(44, 1, 0, 'all', 'message-placeholder', 'How can we help you?', 'como podemos ayudarte?', '어떻게 도와 드릴까요?', '', '', 'Làm thế nào chúng tôi có thể giúp bạn?', '我们该怎样帮助你？', ''),
(45, 1, 0, 'all', 'submit', 'Submit', 'Enviar', '제출', '', '', 'Gửi đi', '提交', ''),
(46, 1, 0, 'contact-us', 'contact-methods', 'Contact Methods', 'Métodos de contacto', '연락 방법', '', '', 'Phương thức liên lạc', '联系方式', ''),
(48, 1, 0, 'all', 'search-placeholder-02', 'ie. appointment, insurance or hours', 'es decir. cita, seguro u horario', '즉. 약속, 보험 또는 시간', '', '', 'I E. cuộc hẹn, bảo hiểm hoặc giờ', '即。 约会，保险或工作时间', ''),
(50, 1, 0, 'all', 'message-form', 'Message Form', 'Formulario de mensaje', '메시지 양식', '', '', 'Mẫu tin nhắn', '留言表格', ''),
(51, 1, 0, 'all', 'message-form-svg-desc-01', 'Click here to minimize the Message Form.', 'Haga clic aquí para minimizar el formulario de mensaje.', '메시지 양식을 최소화하려면 여기를 클릭하십시오.', '', '', 'Nhấn vào đây để thu nhỏ Mẫu tin nhắn.', '单击此处以最小化消息表单。', ''),
(52, 1, 0, 'all', 'message-form-svg-title', 'Minimize Message Form', 'Minimizar formulario de mensaje', '메시지 양식 최소화', '', '', 'Giảm thiểu hình thức tin nhắn', '最小化消息表单', ''),
(53, 1, 0, 'all', 'message-form-svg-desc-02', 'Click here to send us a quick message.', 'Haga clic aquí para enviarnos un mensaje rápido.', '빠른 메시지를 보내려면 여기를 클릭하십시오.', '', '', 'Nhấn vào đây để gửi cho chúng tôi một tin nhắn nhanh chóng.', '单击此处向我们发送快速消息。', ''),
(54, 1, 0, 'all', 'menu', 'Menu', 'Menú', '메뉴', '', '', 'Thực đơn', '菜单', ''),
(55, 1, 0, 'all', 'services', 'Services', 'Servicios', '서비스', '', '', 'Dịch vụ', '服务', ''),
(56, 1, 0, 'all', 'back', 'Back', 'atrás', '뒤', '', '', 'Trở lại', '背部', ''),
(57, 1, 0, 'all', 'name-error-req', 'Please enter your name above.', 'Por favor ingrese su nombre arriba.', '위에 이름을 입력하십시오.', '', '', 'Vui lòng nhập tên của bạn ở trên.', '请在上方输入您的姓名。', ''),
(58, 1, 0, 'all', 'name-error-minMax', 'This field must contain between 2 to 32 characters.', 'Este campo debe contener entre 2 y 32 caracteres.', '이 필드는 2-32 자 사이 여야합니다.', '', '', 'Trường này phải chứa từ 2 đến 32 ký tự.', '此字段必须包含2到32个字符。', ''),
(59, 1, 0, 'all', 'name-error-badChar', 'The following characters are not permited in this form.  ( > < & # )  Please remove before submitting.', 'Los siguientes caracteres no están permitidos en este formulario. (> <& #) Eliminar antes de enviar.', '이 형식에서는 다음 문자를 사용할 수 없습니다. (> <& #) 제출하기 전에 제거하십시오.', '', '', 'Các ký tự sau đây không được phép trong mẫu này. (> <& #) Vui lòng xóa trước khi gửi.', '此格式不允许使用以下字符。 （> <＆＃）请删除后再提交。', ''),
(60, 1, 0, 'all', 'email-error-req', 'Please enter a valid email address.', 'Por favor, introduce una dirección de correo electrónico válida.', '유효한 이메일 주소를 입력하세요.', '', '', 'Vui lòng nhập một địa chỉ email hợp lệ.', '请输入有效的电子邮件地址。', ''),
(61, 1, 0, 'all', 'email-error-max', 'Email address must be 256 characters or less.', 'La dirección de correo electrónico debe tener 256 caracteres o menos.', '이메일 주소는 256 자 이하 여야합니다.', '', '', 'Địa chỉ email phải có 256 ký tự trở xuống.', '电子邮件地址不得超过256个字符。', ''),
(62, 1, 0, 'all', 'message-error-req', 'What can we help you with?', '¿Qué podemos ayudarte?', '우리는 당신에게 어떤 도움을 줄 수?', '', '', 'Chúng tôi có thể giúp gì cho bạn?', '我们能帮到你什么？', ''),
(63, 1, 0, 'all', 'message-error-max', 'Messages must be 512 characters or less.', 'Los mensajes deben tener 512 caracteres o menos.', '메시지는 512 자 이하 여야합니다.', '', '', 'Tin nhắn phải có 512 ký tự trở xuống.', '邮件不得超过512个字符。', ''),
(64, 1, 0, 'all', 'grecaptcha-error', 'Please prove that you are not a robot.', 'Por favor prueba que no eres un robot.', '로봇이 아님을 증명하십시오.', '', '', 'Hãy chứng minh rằng bạn không phải là robot.', '请证明您不是机器人。', ''),
(65, 1, 0, 'all', 'services-browse', 'Browse Services', 'Buscar servicios', '서비스 찾아보기', '', '', 'Duyệt dịch vụ', '浏览服务', ''),
(66, 1, 0, 'all', 'about-us-browse', 'Browse About Us', 'Navegar sobre nosotros', '회사 소개', '', '', 'Duyệt về chúng tôi', '浏览关于我们', ''),
(67, 1, 0, 'all', 'languages-browse', 'Browse Languages', 'Examinar idiomas', '언어 찾아보기', '', '', 'Duyệt ngôn ngữ', '浏览语言', ''),
(68, 1, 0, 'contact-us', 'gen-family', 'General & Family Dentistry', 'Odontología general y familiar', '일반 및 가족 치과', '', '', 'Nha khoa tổng quát và gia đình', '一般家庭牙科', ''),
(69, 1, 0, 'contact-us', 'view-large', 'View Larger Map', 'Ver mapa más grande', '큰지도로보기', '', '', 'xem bản đồ lớn hơn', '查看更大的地图', ''),
(70, 1, 0, 'index', 'text-01', 'At ICONIC Dentistry we serve patients with the goal of achieving better oral and systemic health.  With a combined, over 50 years of clinical and research experience from both the private and public sectors, our doctors are aptly qualified for this goal. In addition, our team provides a friendly, comfortable and supportive environment that’s greatly conducive to healing and recovery.', 'En ICONIC Dentistry atendemos a pacientes con el objetivo de lograr una mejor salud oral y sistémica. Con una experiencia combinada de más de 50 años de experiencia clínica y de investigación en los sectores público y privado, nuestros médicos están calificados para este objetivo. Además, nuestro equipo proporciona un entorno amigable, cómodo y de apoyo que es muy propicio para la curación y la recuperación.', 'ICONIC Dentistry는보다 나은 구강 건강과 전신 건강을 목표로 환자를 지원합니다. 민간 및 공공 부문에서 50 년이 넘는 임상 및 연구 경험을 결합한 의사들은이 목표에 적합한 자격을 갖추고 있습니다. 또한, 우리 팀은 치유와 회복에 크게 도움이되는 친절하고 편안하며 지원적인 환경을 제공합니다.', '', '', 'Tại Nha khoa ICONIC, chúng tôi phục vụ bệnh nhân với mục tiêu đạt được sức khỏe răng miệng và hệ thống tốt hơn. Với sự kết hợp, hơn 50 năm kinh nghiệm lâm sàng và nghiên cứu từ cả khu vực tư nhân và công cộng, các bác sĩ của chúng tôi có đủ điều kiện phù hợp cho mục tiêu này. Ngoài ra, nhóm của chúng tôi cung cấp một môi trường thân thiện, thoải mái và hỗ trợ mà rất thuận lợi cho việc chữa lành và phục hồi.', '在ICONIC牙科，我们为患者服务，以实现更好的口腔和全身健康。 凭借来自私营和公共部门超过50年的临床和研究经验，我们的医生非常适合实现这一目标。 此外，我们的团队提供了一个友好，舒适和支持性的环境，极大地有助于康复和康复。', ''),
(71, 1, 0, 'search', 'description', 'ICONIC Dentistries Search page.', '', '', '', '', '', '', ''),
(72, 1, 0, 'privacy-policy', 'description', 'ICONIC Dentistries Privacy Policy page.', '', '', '', '', '', '', ''),
(73, 1, 0, 'contact-us', 'description', 'ICONIC Dentistries Contact Us page.', '', '', '', '', '', '', ''),
(74, 1, 0, 'all', 'holistic-and-integrative', 'Holistic and Integrative', '', '', '', '', '', '', ''),
(75, 1, 0, 'holistic-and-integrative', 'description', 'ICONIC Dentistries Holistic and Integrative page.', '', '', '', '', '', '', ''),
(76, 1, 0, 'about-us', 'description', 'ICONIC Dentistries About Us page.', '', '', '', '', '', '', ''),
(77, 1, 0, 'meet-the-team', 'description', 'ICONIC Dentistries Meet the Team page.', '', '', '', '', '', '', ''),
(78, 1, 0, 'all', 'meet-the-team', 'Meet the Team', '', '', '', '', '', '', ''),
(79, 1, 0, 'all', 'our-philosophy', 'Our Philosophy', '', '', '', '', '', '', ''),
(80, 1, 0, 'our-philosophy', 'description', 'ICONIC Dentistries Our Philosophy page.', '', '', '', '', '', '', ''),
(81, 1, 0, 'all', 'past-and-present', 'Past and  Present', '', '', '', '', '', '', ''),
(82, 1, 0, 'past-and-present', 'description', 'ICONIC Dentistries Past and Present page.', '', '', '', '', '', '', ''),
(83, 1, 0, 'all', 'for-patients', 'For Patients', '', '', '', '', '', '', ''),
(84, 1, 0, 'for-patients', 'description', 'ICONIC Dentistries For Patients page.', '', '', '', '', '', '', ''),
(85, 1, 0, 'all', 'faq', 'Frequently Asked Questions (FAQs)', '', '', '', '', '', '', ''),
(86, 1, 0, 'faq', 'description', 'ICONIC Dentistries Frequently Asked Questions (FAQs) page.', '', '', '', '', '', '', ''),
(87, 1, 0, 'all', 'forms', 'Downloadable Forms', '', '', '', '', '', '', ''),
(88, 1, 0, 'forms', 'description', 'ICONIC Dentistries Downloadable Forms page.', '', '', '', '', '', '', ''),
(89, 1, 0, 'all', 'languages', 'Languages', '', '', '', '', '', '', ''),
(90, 1, 0, 'languages', 'description', 'ICONIC Dentistries Languages page.', '', '', '', '', '', '', ''),
(91, 1, 0, 'all', 'services', 'Services', '', '', '', '', '', '', ''),
(92, 1, 0, 'services', 'description', 'ICONIC Dentistries Services page.', '', '', '', '', '', '', ''),
(93, 1, 0, 'all', 'esthetic-dentistry', 'Esthetic Dentistry', '', '', '', '', '', '', ''),
(94, 1, 0, 'esthetic-dentistry', 'description', 'ICONIC Dentistries Esthetic Dentistry page.', '', '', '', '', '', '', ''),
(95, 1, 0, 'all', 'family-dentistry', 'Family Dentistry', '', '', '', '', '', '', ''),
(96, 1, 0, 'family-dentistry', 'description', 'ICONIC Dentistries Family Dentistry page.', '', '', '', '', '', '', ''),
(97, 1, 0, 'all', 'implant-dentistry', 'Implant Dentistry', '', '', '', '', '', '', ''),
(98, 1, 0, 'implant-dentistry', 'description', 'ICONIC Dentistries Implant Dentistry page.', '', '', '', '', '', '', ''),
(99, 1, 0, 'all', 'laser-dentistry', 'Laser Dentistry', '', '', '', '', '', '', ''),
(100, 1, 0, 'laser-dentistry', 'description', 'ICONIC Dentistries Laser Dentistry page.', '', '', '', '', '', '', ''),
(101, 1, 0, 'all', 'orthodontics', 'Orthodontics', '', '', '', '', '', '', ''),
(102, 1, 0, 'orthodontics', 'description', 'ICONIC Dentistries Orthodontics page.', '', '', '', '', '', '', ''),
(103, 1, 0, 'all', 'ozone-therapy', 'Ozone Therapy', '', '', '', '', '', '', ''),
(104, 1, 0, 'ozone-therapy', 'description', 'ICONIC Dentistries Ozone Therapy page.', '', '', '', '', '', '', ''),
(105, 1, 0, 'all', 'offline', 'Offline', '', '', '', '', '', '', ''),
(106, 1, 0, 'all', 'search-text-02', 'Enter keywords or phrases to search.', 'Ingrese palabras clave o frases para buscar.', '검색 할 키워드 또는 구문을 입력하십시오.', '', '', 'Nhập từ khóa hoặc cụm từ để tìm kiếm.', '输入关键词或短语进行搜索。', '輸入關鍵詞或短語進行搜索。');

-- --------------------------------------------------------

--
-- Table structure for table `ccms_lng_charset`
--

CREATE TABLE `ccms_lng_charset` (
  `id` int(11) NOT NULL,
  `lngDesc` char(63) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `lng` char(5) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `dir` char(3) NOT NULL COMMENT 'Character direction.  ltr = left to right, rtl = right to left',
  `ptrLng` char(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ccms_lng_charset`
--

INSERT INTO `ccms_lng_charset` (`id`, `lngDesc`, `status`, `lng`, `default`, `dir`, `ptrLng`) VALUES
(1, 'Deutsch (German)', 0, 'de', 0, 'ltr', ''),
(2, 'Deutsch (Austria)', 0, 'de-at', 0, '', 'de'),
(3, 'Deutsch (Germany)', 0, 'de-de', 0, '', 'de'),
(4, 'Deutsch (Liechtenstein)', 0, 'de-li', 0, '', 'de'),
(5, 'Deutsch (Luxembourg)', 0, 'de-lu', 0, '', 'de'),
(6, 'Deutsch (Switzerland)', 0, 'de-ch', 0, '', 'de'),
(7, 'English', 1, 'en', 1, 'ltr', ''),
(8, 'English (Australia)', 0, 'en-au', 0, '', 'en'),
(9, 'English (Belize)', 0, 'en-bz', 0, '', 'en'),
(10, 'English (Canada)', 0, 'en-ca', 0, '', 'en'),
(11, 'English (Ireland)', 0, 'en-ie', 0, '', 'en'),
(12, 'English (Jamaica)', 0, 'en-jm', 0, '', 'en'),
(13, 'English (New Zealand)', 0, 'en-nz', 0, '', 'en'),
(14, 'English (Philippines)', 0, 'en-ph', 0, '', 'en'),
(15, 'English (South Africa)', 0, 'en-za', 0, '', 'en'),
(16, 'English (Trinidad and Tobago)', 0, 'en-tt', 0, '', 'en'),
(17, 'English (United Kingdom)', 0, 'en-gb', 0, '', 'en'),
(18, 'English (United States)', 0, 'en-us', 0, '', 'en'),
(19, 'English (Zimbabwe)', 0, 'en-zw', 0, '', 'en'),
(20, 'Español (Spanish)', 0, 'es', 0, 'ltr', ''),
(21, 'Español (Argentina)', 0, 'es-ar', 0, '', 'es'),
(22, 'Español (Bolivia)', 0, 'es-bo', 0, '', 'es'),
(23, 'Español (Chile)', 0, 'es-cl', 0, '', 'es'),
(24, 'Español (Colombia)', 0, 'es-co', 0, '', 'es'),
(25, 'Español (Costa Rica)', 0, 'es-cr', 0, '', 'es'),
(26, 'Español (Dominican Republic)', 0, 'es-do', 0, '', 'es'),
(27, 'Español (Ecuador)', 0, 'es-ec', 0, '', 'es'),
(28, 'Español (El Salvador)', 0, 'es-sv', 0, '', 'es'),
(29, 'Español (Guatemala)', 0, 'es-gt', 0, '', 'es'),
(30, 'Español (Honduras)', 0, 'es-hn', 0, '', 'es'),
(31, 'Español (Mexico)', 0, 'es-mx', 0, 'ltr', 'es'),
(32, 'Español (Nicaragua)', 0, 'es-ni', 0, '', 'es'),
(33, 'Español (Panama)', 0, 'es-pa', 0, '', 'es'),
(34, 'Español (Paraguay)', 0, 'es-py', 0, '', 'es'),
(35, 'Español (Peru)', 0, 'es-pe', 0, '', 'es'),
(36, 'Español (Puerto Rico)', 0, 'es-pr', 0, '', 'es'),
(37, 'Español (Spain)', 0, 'es-es', 0, '', 'es'),
(38, 'Español (Uruguay)', 0, 'es-uy', 0, '', 'es'),
(39, 'Español (Venezuela)', 0, 'es-ve', 0, '', 'es'),
(40, 'Français (French)', 0, 'fr', 0, 'ltr', ''),
(41, 'Français (Belgium)', 0, 'fr-be', 0, '', 'fr'),
(42, 'Français (Canada)', 0, 'fr-ca', 0, '', 'fr'),
(43, 'Français (France)', 0, 'fr-fr', 0, '', 'fr'),
(44, 'Français (Luxembourg)', 0, 'fr-lu', 0, '', 'fr'),
(45, 'Français (Monaco)', 0, 'fr-mc', 0, '', 'fr'),
(46, 'Français (Switzerland)', 0, 'fr-ch', 0, '', 'fr'),
(47, 'Melayu Indonesia (Malay Indonesian)', 0, 'ms', 0, 'ltr', ''),
(48, 'Português (Portuguese)', 0, 'pt', 0, 'ltr', ''),
(49, 'العربية (Arabic)', 0, 'ar', 0, 'rtl', ''),
(50, 'বাঙ্গালী (Bengali)', 0, 'bn', 0, 'ltr', ''),
(51, 'Pусский (Russian)', 0, 'ru', 0, 'ltr', ''),
(52, 'हिन्दी (Hindi)', 0, 'hi', 0, 'ltr', ''),
(53, '日本語 (Japanese)', 0, 'ja', 0, 'ltr', ''),
(54, '中国 (Chinese)', 0, 'zh', 0, '', 'zh-cn'),
(55, '中文 (Chinese, Simplified)', 0, 'zh-cn', 0, 'ltr', ''),
(56, '中文 (Chinese,Traditional)', 0, 'zh-tw', 0, '', 'zh-cn'),
(57, 'עברית (Hebrew)', 0, 'he', 0, 'rtl', ''),
(59, 'Norwegian (Bokmal)', 0, 'nb-no', 0, 'ltr', ''),
(60, '한국어 (Korean)', 0, 'ko', 0, 'ltr', ''),
(61, '한국어, 북한 (Korean, North)', 0, 'ko-kp', 0, 'ltr', 'ko'),
(62, '한국어, 한국 (Korean, South)', 0, 'ko-kr', 0, 'ltr', 'ko'),
(63, 'Tiếng Việt (Vietnamese)', 0, 'vi', 0, 'ltr', '');

-- --------------------------------------------------------

--
-- Table structure for table `ccms_log`
--

CREATE TABLE `ccms_log` (
  `id` int(11) NOT NULL,
  `date` int(20) NOT NULL,
  `ip` char(16) NOT NULL,
  `url` varchar(512) NOT NULL,
  `log` varchar(1024) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='Records saved here contain details related to possible session highjacking attempts.';

-- --------------------------------------------------------

--
-- Table structure for table `ccms_password_recovery`
--

CREATE TABLE `ccms_password_recovery` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `exp` int(20) UNSIGNED NOT NULL,
  `ip` char(16) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_agent` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ccms_user`
--

CREATE TABLE `ccms_user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `hash` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'The hashed version of the users actual password.',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 = inactive, 1 = active',
  `alias` varchar(32) NOT NULL COMMENT 'nick name / not a login name',
  `super` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0=not super user, 1=super user',
  `priv` varchar(1024) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `position` varchar(128) NOT NULL,
  `phone1` varchar(64) NOT NULL,
  `phone2` varchar(64) NOT NULL,
  `facebook` varchar(128) NOT NULL,
  `skype` varchar(32) NOT NULL,
  `note` varchar(1024) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) NOT NULL,
  `prov_state` varchar(32) NOT NULL,
  `country` varchar(64) NOT NULL,
  `post_zip` varchar(32) NOT NULL,
  `nav_toggle` tinyint(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='Contains the login settings for fully registered users.';

-- --------------------------------------------------------

--
-- Table structure for table `sri`
--

CREATE TABLE `sri` (
  `id` int(11) NOT NULL,
  `url` varchar(512) NOT NULL,
  `sri-code` varchar(256) NOT NULL COMMENT 'Subresource Integrity Code'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='Used to temporarily store Subresource Integrity codes';

--
-- Dumping data for table `sri`
--

INSERT INTO `sri` (`id`, `url`, `sri-code`) VALUES
(1, 'https://d23cij6660kk94.cloudfront.net/ccmstpl/_css/style-ltr.css', 'JTa23kX0a97GzGe+xxH1lEYURgn+D9v2/dxDxlN5mGI='),
(2, 'https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/main.js', 'jHn07zBxhNurtitiJx0luN9LZrPa5wVAK6i0FsC1aAQ='),
(3, 'https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-validate-additional-methods-1.19.0.min.js', 'JsVwBM7WSelnjtl0T8H3/JP3te7+uWLgR6McTFu04nU='),
(4, 'https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-validate-1.19.0.min.js', '1kkpIg8uneE7Y8ZsEmbdTxu/iHfMoExDaRbNgqdXbN0='),
(5, 'https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/jquery.mobile.custom.min.js', 'fLTv112EFCDDKgf1iA9TwbWaeKLKIeTIBaahDA8a1Ck='),
(6, 'https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-3.5.1.min.js', '9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0='),
(7, 'https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/modernizr-3.6.0-custom-min.js', '1EQSYmPMi3KZvDWx77cBsdSb+N1blZdt043snER7HLQ='),
(8, 'https://d23cij6660kk94.cloudfront.net/ccmsusr/_css/animate.min.css', 'Dff67Ehq5cfVp7f7xBLDivriXQO6YpPLYSfEX4f6hmk='),
(9, 'https://d23cij6660kk94.cloudfront.net/ccmstpl/_css/owl.carousel-2.3.4.min.css', 'XfkLQRI4+TnBNB1XAfg+4vcbcT3ZemMYG02w6IUQ4eE='),
(10, 'https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/owl.carousel.min.js', 'fnhIBVG0t4p7esh+T0IJU9utTwdy2EdHgOyJaOce4MY=');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ccms_blacklist`
--
ALTER TABLE `ccms_blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ccms_cache`
--
ALTER TABLE `ccms_cache`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ccms_headers`
--
ALTER TABLE `ccms_headers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ccms_ins_db`
--
ALTER TABLE `ccms_ins_db`
  ADD PRIMARY KEY (`id`),
  ADD KEY `CCMS_insDBPreload_idx` (`status`,`access`,`grp`);

--
-- Indexes for table `ccms_lng_charset`
--
ALTER TABLE `ccms_lng_charset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ccms_log`
--
ALTER TABLE `ccms_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ccms_password_recovery`
--
ALTER TABLE `ccms_password_recovery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ccms_user`
--
ALTER TABLE `ccms_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `sri`
--
ALTER TABLE `sri`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ccms_blacklist`
--
ALTER TABLE `ccms_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ccms_cache`
--
ALTER TABLE `ccms_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ccms_headers`
--
ALTER TABLE `ccms_headers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ccms_ins_db`
--
ALTER TABLE `ccms_ins_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `ccms_lng_charset`
--
ALTER TABLE `ccms_lng_charset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `ccms_log`
--
ALTER TABLE `ccms_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ccms_password_recovery`
--
ALTER TABLE `ccms_password_recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ccms_user`
--
ALTER TABLE `ccms_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sri`
--
ALTER TABLE `sri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;
