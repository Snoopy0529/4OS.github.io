-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2024 at 04:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rposystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `rpos_admin`
--

CREATE TABLE `rpos_admin` (
  `admin_id` varchar(200) NOT NULL,
  `admin_name` varchar(200) NOT NULL,
  `admin_email` varchar(200) NOT NULL,
  `admin_password` varchar(200) NOT NULL,
  `profile_image` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_admin`
--

INSERT INTO `rpos_admin` (`admin_id`, `admin_name`, `admin_email`, `admin_password`, `profile_image`) VALUES
('10e0b6dc958adfb5b094d8935a13aeadbe783c25', 'Drixx', 'admin@admin.com', '$2y$10$ZSdUN1NgXQ3kTq3STfDsseM5r6GGVz4B9/Yi1k7udV8q7Og5rVLHK', '');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_customers`
--

CREATE TABLE `rpos_customers` (
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phoneno` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `customer_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_customers`
--

INSERT INTO `rpos_customers` (`customer_id`, `customer_name`, `customer_phoneno`, `customer_email`, `customer_password`, `created_at`) VALUES
('44d3533b25bb', 'drix', '123123123', 'drix@customer.com', '875253de1c1dfdeb4f2eb0ee418ff3a793700160', '2024-04-23 07:56:11.108214'),
('5132ff5f8802', 'Vandave', '12312313212', 'vandave@mail.com', '03d000df4fa813c9d0c93e59a0ba3b6dc5c88399', '2024-03-28 18:59:38.312319'),
('6425306c74bc', 'drix2', '1231241231', 'drix2@customer.com', 'fa2c19fbdb6cbe39e7ff4643e4a3a6cb552b4c4d', '2024-04-23 08:54:38.064690'),
('c4c963942069', 'Kaede Rhykee Miranda', '123545613', 'kaederm@customer.com', 'bbfb74ac634f1833a114424174699212bf3ef593', '2024-04-02 22:56:08.421750');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_inventory`
--

CREATE TABLE `rpos_inventory` (
  `ingredients_id` int(11) NOT NULL,
  `ing_name` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `qty` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rpos_inventory`
--

INSERT INTO `rpos_inventory` (`ingredients_id`, `ing_name`, `qty`, `created_at`) VALUES
(1, 'Lettuce', '0', '2024-05-02 14:57:06.339882'),
(6, 'Rice', '500', '2024-04-30 03:21:50.511803'),
(7, 'Nori Wrapper', '200', '2024-04-30 03:22:04.454261'),
(8, 'Yellow Radish', '300', '2024-04-30 03:22:16.379553'),
(9, 'Sesame Seeds', '100', '2024-04-30 03:22:37.452576'),
(10, 'Ramyun Noodles', '500', '2024-04-30 03:22:48.806179'),
(11, 'Japchae Noodles', '100', '2024-04-30 03:22:59.250448'),
(12, 'Cabbage', '250', '2024-04-30 03:23:08.881461'),
(13, 'Tomato', '100', '2024-04-30 03:23:20.273872'),
(14, 'Onions', '100', '2024-04-30 03:23:28.822603'),
(15, 'Garlic', '100', '2024-04-30 03:23:36.156971'),
(16, 'Salt', '1000', '2024-04-30 03:23:43.662722'),
(17, 'Pepper', '500', '2024-04-30 03:23:53.122850'),
(18, 'Kimchi', '300', '2024-04-30 03:25:47.733731'),
(19, 'Cucumber', '50', '2024-04-30 03:25:55.876517'),
(20, 'French Fries', '50', '2024-04-30 03:26:08.858303'),
(21, 'Beef', '200', '2024-04-30 03:26:21.945099'),
(22, 'Tuna', '50', '2024-04-30 03:26:32.664501'),
(23, 'Chicken Meat', '200', '2024-04-30 03:26:51.035006'),
(24, 'Pork Meat', '200', '2024-04-30 03:27:01.176952'),
(25, 'Imitation Crab Meat', '100', '2024-04-30 03:27:14.699100'),
(26, 'Bacon', '200', '2024-04-30 03:27:24.462775'),
(27, 'Chicken Fillet', '200', '2024-04-30 03:27:36.858819'),
(28, 'Egg', '300', '2024-04-30 03:27:44.573874'),
(32, 'Caviar', '6', '2024-05-02 14:55:49.870185');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_kiosk_orders`
--

CREATE TABLE `rpos_kiosk_orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `customer_name` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `prod_id` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `prod_name` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `prod_price` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `prod_qty` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `order_status` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rpos_kiosk_orders`
--

INSERT INTO `rpos_kiosk_orders` (`order_id`, `customer_id`, `customer_name`, `prod_id`, `prod_name`, `prod_price`, `prod_qty`, `order_status`, `created_at`) VALUES
(22, 'XRPXAoRg', 'Walk-In Customer', '752b85636e', 'Ramen', '100', '2', 'Paid', '2024-04-27 14:56:23.123983'),
(25, 'm8fzwqKF', 'Walk-In Customer', '87a67f5167', 'hotdog', '25', '8', 'Paid', '2024-04-27 14:51:55.044220'),
(26, 'cRkAGTmR', 'Walk-In Customer', '752b85636e', 'Ramen', '100', '12', 'Paid', '2024-04-27 14:50:44.136938'),
(27, 'f8SHfPiV', 'Walk-In Customer', '484de89bfd', 'Corned Beef', '99.99', '1', 'Paid', '2024-04-27 15:03:59.470072'),
(28, 'TbTjdIT4', 'Walk-In Customer', 'b010c78ea5', 'Combo KR1', '90', '10', 'Paid', '2024-04-27 14:59:04.627015'),
(31, 'EPoj8qnE', 'Walk-In Customer', '484de89bfd', 'Corned Beef', '99.99', '1', 'Paid', '2024-04-29 11:46:22.542717'),
(34, 'w7laqUl6', 'Walk-In Customer', '8a4aa84a76', 'Drix', '20', '20', 'Paid', '2024-04-29 11:57:52.448303'),
(37, 'YnBIPe2p', 'Walk-In Customer', 'ffefc065e3', 'Egg', '200', '1', 'Paid', '2024-04-29 12:08:30.120717'),
(39, 'oWgS32MH', 'Walk-In Customer', 'e97284d743', 'Josh', '5000', '1', 'Paid', '2024-04-29 15:14:32.271681'),
(40, 'E8KUOr8k', 'Walk-In Customer', 'b010c78ea5', 'Combo KR1', '90', '5', 'Paid', '2024-04-29 16:53:10.263874'),
(42, 'y8uPQSpM', 'Walk-In Customer', '87a67f5167', 'hotdog', '25', '2', 'Paid', '2024-04-29 17:49:19.632651'),
(44, '2jGgusVw', 'Walk-In Customer', '484de89bfd', 'Corned Beef', '99.99', '1', 'Paid', '2024-04-29 18:23:59.867246'),
(45, 'lAMdkALG', 'Walk-In Customer', '752b85636e', 'Ramen', '100', '1', 'Paid', '2024-04-29 18:26:27.095339'),
(46, 'v2deadga', 'Walk-In Customer', 'VSKCU', 'taerqwe', '200', '5', 'Paid', '2024-05-01 06:15:59.814220');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_orders`
--

CREATE TABLE `rpos_orders` (
  `order_id` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `prod_id` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `prod_qty` varchar(200) NOT NULL,
  `order_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_orders`
--

INSERT INTO `rpos_orders` (`order_id`, `order_code`, `customer_id`, `customer_name`, `prod_id`, `prod_name`, `prod_price`, `prod_qty`, `order_status`, `created_at`) VALUES
('64ab9e9a29', 'HGDT-1940', '44d3533b25bb', 'drix', 'V7TFK', 'qwe2', '22', '1', 'Paid', '2024-05-02 14:57:06.337299'),
('9d32f53ce7', 'MKVG-7523', '44d3533b25bb', 'drix', 'F6B2W', 'qwe1', '11', '1', 'Paid', '2024-05-02 14:55:49.867666');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_pass_resets`
--

CREATE TABLE `rpos_pass_resets` (
  `reset_id` int(20) NOT NULL,
  `reset_code` varchar(200) NOT NULL,
  `reset_token` varchar(200) NOT NULL,
  `reset_email` varchar(200) NOT NULL,
  `reset_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_pass_resets`
--

INSERT INTO `rpos_pass_resets` (`reset_id`, `reset_code`, `reset_token`, `reset_email`, `reset_status`, `created_at`) VALUES
(1, '63KU9QDGSO', '4ac4cee0a94e82a2aedc311617aa437e218bdf68', 'sysadmin@icofee.org', 'Pending', '2020-08-17 15:20:14.318643');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_payments`
--

CREATE TABLE `rpos_payments` (
  `pay_id` varchar(200) NOT NULL,
  `pay_code` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `pay_amt` varchar(200) NOT NULL,
  `pay_method` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_payments`
--

INSERT INTO `rpos_payments` (`pay_id`, `pay_code`, `order_code`, `customer_id`, `pay_amt`, `pay_method`, `created_at`) VALUES
('000134', '4UEQ8JTBK2', 'PKFU-3970', '5132ff5f8802', '90', 'Cash', '2024-04-27 14:45:02.107899'),
('0296bb', 'H2K5B73PZQ', 'POEU-7384', '44d3533b25bb', '90', 'Cash', '2024-04-27 15:03:27.472874'),
('0678f7', '4DEBL7J2ZK', 'ZODL-8705', '6425306c74bc', '55', 'Cash', '2024-05-02 11:59:02.991975'),
('0be02e', 'EMPU1XVN8G', 'KNCY-4628', '5132ff5f8802', '200', 'Cash', '2024-05-01 06:17:13.565523'),
('0bed72', '8PNZ3RV9HA', 'MTDU-8635', '5132ff5f8802', '240', 'Paypal', '2024-03-28 19:00:04.947538'),
('0bf592', '9UMWLG4BF8', 'EJKA-4501', '35135b319ce3', '8', 'Cash', '2022-09-04 16:31:54.525284'),
('0ea7de', '3O1ASGKMTN', 'VZMU-8345', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:50:11.805948'),
('0fd119', 'GN7B4P8FL3', 'CGJD-8491', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:40:02.742527'),
('11670e', '5VHXPQJ7L8', '46', 'v2deadga', '1000', 'Cash', '2024-05-01 06:15:59.811276'),
('12e8bb', 'DG74UM2SBX', 'UCVR-0134', '44d3533b25bb', '199.98', 'Cash', '2024-04-27 09:06:36.837942'),
('13b2d5', 'KVC4HFQ7UA', 'MGZQ-0972', '5132ff5f8802', '200', 'Cash', '2024-03-28 19:50:03.512284'),
('176642', 'MK7R2YEW6Z', '44', '2jGgusVw', '99.99', 'Cash', '2024-04-29 18:23:59.864362'),
('206629', 'TJW4X2GN9I', 'ACND-9724', '44d3533b25bb', '360', 'Cash', '2024-04-23 08:55:34.740253'),
('222a9f', 'CKVT453M7Z', 'DGIA-0436', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:55:33.319819'),
('24be44', '72LAUCFYXQ', 'HGDT-1940', '44d3533b25bb', '22', 'Cash', '2024-05-02 14:57:06.334743'),
('2a28cb', '147LR5NGXK', 'YBTX-9705', '44d3533b25bb', '90', 'Cash', '2024-04-27 15:03:51.005958'),
('2fa21f', 'V3UJHYKRZA', 'NZAH-2645', '44d3533b25bb', '99.99', 'Cash', '2024-04-29 18:33:48.878331'),
('30cb8d', '8A2RZ6UQVI', 'VKXB-3097', 'c4c963942069', '55', 'Cash', '2024-05-02 12:17:44.569296'),
('369e06', 'QOVWEFUI3M', 'ILKO-1843', 'c4c963942069', '90', 'Cash', '2024-04-27 10:43:32.530699'),
('3b91e0', '1MUKB8RECT', 'XNCY-4753', '6425306c74bc', '55', 'Cash', '2024-05-02 12:07:20.075490'),
('3ff75a', 'QW7LDP34VA', '26', 'cRkAGTmR', '1200', 'Cash', '2024-04-27 14:50:47.059077'),
('4423d7', 'QWERT0YUZ1', 'JFMB-0731', '35135b319ce3', '11', 'Cash', '2022-09-04 16:37:03.655834'),
('442865', '146XLFSC9V', 'INHG-0875', '9c7fcc067bda', '10', 'Paypal', '2022-09-04 16:35:22.470600'),
('443485', '1CVGYN6FEH', 'IZAG-1846', '44d3533b25bb', '55', 'Cash', '2024-05-02 14:50:12.793700'),
('49cdd2', '31BJDOCGTH', 'JLVG-0231', 'c4c963942069', '55', 'Cash', '2024-05-02 12:11:33.796713'),
('4b85c1', 'JNO4H2PF15', 'LUQY-6503', '5132ff5f8802', '8', 'Cash', '2024-03-28 19:22:20.480798'),
('4e6a02', 'Y8DNUMGPLW', 'CRVP-2657', '5132ff5f8802', '90', 'Cash', '2024-04-27 14:51:40.187764'),
('5127f2', 'LZW6JGBN7U', 'THDL-5230', '44d3533b25bb', '200', 'Cash', '2024-04-27 15:02:39.167935'),
('5201b0', 'PEHBDN4S7R', 'MOQX-0428', '6425306c74bc', '55', 'Cash', '2024-05-02 12:10:33.067416'),
('5570df', 'LP5NXRFES4', '31', 'EPoj8qnE', '99.99', 'Cash', '2024-04-29 11:46:22.540001'),
('5cddfb', 'F8UBPEWYLG', 'CGJD-8491', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:26:58.513738'),
('61c46f', 'J6F2B1QNGX', 'INAO-8409', '5132ff5f8802', '200', 'Cash', '2024-03-30 10:03:29.413044'),
('65891b', 'MF2TVJA1PY', 'ZPXD-6951', 'e711dcc579d9', '16', 'Cash', '2022-09-03 13:12:46.959558'),
('6955aa', 'LGE7RJ8Q3U', 'YAXE-9460', '1fc1f694985d', '4', 'Cash', '2024-03-28 19:29:27.246206'),
('6f7782', '6XEHJTZROG', 'VZMU-8345', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:50:03.217600'),
('6fc08a', 'AR9WTJGPIO', 'VZMU-8345', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:47:25.188596'),
('73eeec', 'TF1VROJMYN', 'JYRX-4908', '5132ff5f8802', '200', 'Cash', '2024-04-30 06:03:31.489236'),
('75ae21', '1QIKVO69SA', 'IUSP-9453', 'fe6bb69bdd29', '10', 'Cash', '2022-09-03 11:50:40.496625'),
('779fd8', 'DM2TN6PWX5', 'VZMU-8345', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:46:28.431236'),
('78047b', '1MOAX7GRJ2', '34', 'w7laqUl6', '400', 'Cash', '2024-04-29 11:57:52.445651'),
('789f07', 'O5CDF8IGK7', 'GXYH-2097', '44d3533b25bb', '1000', 'Cash', '2024-04-27 14:58:57.434299'),
('7c8606', 'H4I3Y7AJLG', '40', 'E8KUOr8k', '450', 'Cash', '2024-04-29 16:53:10.261498'),
('7cf569', 'JKLFDPSAV8', 'QLWM-4136', '5132ff5f8802', '40', 'Cash', '2024-04-27 14:47:21.072420'),
('7e1989', 'KLTF3YZHJP', 'QOEH-8613', '29c759d624f9', '9', 'Cash', '2022-09-03 12:02:32.926529'),
('7ea224', 'NFGUQ1HK5I', '27', 'f8SHfPiV', '99.99', 'Cash', '2024-04-27 15:03:59.467499'),
('81a7b9', 'NM5HSD78A2', 'CGJD-8491', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:33:18.553954'),
('86a5b5', '4QIJFU5K6D', 'JCZB-9574', '5132ff5f8802', '8', 'Cash', '2024-03-28 19:15:25.390183'),
('892788', 'QSIMVPY3D6', '28', 'TbTjdIT4', '900', 'Cash', '2024-04-27 14:59:04.624322'),
('8a6c43', 'UWCS4YN71O', '37', 'YnBIPe2p', '200', 'Cash', '2024-04-29 12:08:30.118194'),
('8cd642', 'G8WX7RSDFP', 'TBVJ-7834', '44d3533b25bb', '1890', 'Cash', '2024-04-27 14:56:14.172401'),
('8deef1', 'DT4MFQROJ6', '22', 'XRPXAoRg', '200', 'Cash', '2024-04-27 14:56:23.117238'),
('935a2d', 'ZLEHTASGB8', 'CGJD-8491', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:33:37.396838'),
('9494d5', 'KFN6AXQ24V', 'ZJPF-5821', '44d3533b25bb', '5000', 'Cash', '2024-04-29 17:26:42.281907'),
('952b31', 'BEYFAIGCXP', '26', 'cRkAGTmR', '1200', 'Cash', '2024-04-27 14:50:44.134080'),
('968488', '5E31DQ2NCG', 'COXP-6018', '7c8f2100d552', '22', 'Cash', '2022-09-03 12:17:44.639979'),
('984539', 'LSBNK1WRFU', 'FNAB-9142', '35135b319ce3', '18', 'Paypal', '2022-09-04 16:32:14.852482'),
('9c9e3c', 'ZV6UFKE1QI', 'VZMU-8345', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:42:27.404824'),
('9cddee', 'S1EGMIBYTK', 'BEQJ-5613', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:15:34.179286'),
('9f7460', 'V7XZM43HTP', 'PLGE-6312', '44d3533b25bb', '99.99', 'Cash', '2024-04-23 07:56:44.446892'),
('9fcee7', 'AZSUNOKEI7', 'OTEV-8532', '3859d26cd9a5', '15', 'Cash', '2022-09-03 13:13:38.855058'),
('a62399', 'BEYFAIGCXP', '26', 'cRkAGTmR', '1200', 'Cash', '2024-04-27 14:47:31.155959'),
('ae033f', 'RQ6JYGSXN9', '39', 'oWgS32MH', '5000', 'Cash', '2024-04-29 15:14:32.268560'),
('b0fd9d', '1NGBMWS2J4', 'TVQG-1793', '44d3533b25bb', '90', 'Cash', '2024-04-27 15:02:17.352061'),
('b58199', 'AR4ZM1VH7X', 'NZHM-3684', '44d3533b25bb', '231', 'Cash', '2024-04-30 07:25:21.386676'),
('b81ad5', '4LI8JZX5GF', 'JXYE-6354', '27d3fb882358', '6', 'Cash', '2024-03-29 07:35:05.649606'),
('bd22ff', 'G6J512QA3Y', '26', 'cRkAGTmR', '1200', 'Cash', '2024-04-27 14:44:45.966570'),
('bd88ec', '86AKQCES1T', '25', 'm8fzwqKF', '200', 'Cash', '2024-04-27 14:51:55.041508'),
('c7dae7', 'D4CSP7LETI', '45', 'lAMdkALG', '100', 'Cash', '2024-04-29 18:26:27.093074'),
('c81d2e', 'WERGFCXZSR', 'AEHM-0653', '06549ea58afd', '8', 'Cash', '2022-09-03 13:26:00.331494'),
('cafa5c', 'J4Y715KHLS', 'MQJL-8067', '5132ff5f8802', '400', 'Cash', '2024-03-28 19:45:28.182942'),
('d20f74', 'I3QNO7LBT2', 'FBKS-6071', 'c4c963942069', '199.98', 'Cash', '2024-04-27 14:50:54.185444'),
('d6f680', '1FMVTOPXSI', 'DVXJ-7834', '44d3533b25bb', '1000', 'Cash', '2024-04-27 14:33:40.899914'),
('dc78a6', '4GF8MDOA92', 'MKVG-7523', '44d3533b25bb', '11', 'Cash', '2024-05-02 14:55:49.865368'),
('e0b40b', '1349RYEW6D', 'CGJD-8491', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:29:55.865315'),
('e46e29', 'QMCGSNER3T', 'ONSY-2465', '57b7541814ed', '12', 'Cash', '2022-09-03 08:35:50.172062'),
('e50d09', 'HDOYRJZTPF', 'EISN-3207', '1fc1f694985d', '99.99', 'Cash', '2024-03-30 10:04:31.147203'),
('e735be', 'LDETJOYUQK', '42', 'y8uPQSpM', '50', 'Cash', '2024-04-29 17:49:19.630034'),
('ebe81f', 'PVOTSMQ359', 'WFJU-6720', '5132ff5f8802', '200', 'Cash', '2024-03-28 19:27:24.235946'),
('ef8503', 'LYISU2O8XQ', 'YOGA-5219', 'c4c963942069', '99.99', 'Cash', '2024-04-27 14:41:22.080929'),
('f11e8c', 'RWVT435XAN', 'VZMU-8345', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:52:43.281603'),
('f69be2', 'PX4KMEOHSJ', '26', 'cRkAGTmR', '1200', 'Cash', '2024-04-27 14:45:10.267025'),
('f76c45', 'Z5SNMGDLB3', 'CGJD-8491', '44d3533b25bb', '55', 'Cash', '2024-05-02 11:36:36.337711');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_products`
--

CREATE TABLE `rpos_products` (
  `prod_id` varchar(200) NOT NULL,
  `prod_code` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_img` varchar(200) NOT NULL,
  `prod_desc` longtext NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `ingredients` varchar(200) NOT NULL,
  `availability` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_products`
--

INSERT INTO `rpos_products` (`prod_id`, `prod_code`, `prod_name`, `prod_img`, `prod_desc`, `prod_price`, `ingredients`, `availability`, `created_at`) VALUES
('F6B2W', 'PYAB-3469', 'qwe1', '', '111', '11', '', 'Low on Stock', '2024-05-02 14:53:51.207603'),
('V7TFK', 'OMFH-2081', 'qwe2', '', '22', '22', '', 'Available', '2024-05-02 14:54:15.555452');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_product_ingredients`
--

CREATE TABLE `rpos_product_ingredients` (
  `id` int(11) NOT NULL,
  `prod_id` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `ing_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rpos_product_ingredients`
--

INSERT INTO `rpos_product_ingredients` (`id`, `prod_id`, `ing_name`, `quantity`) VALUES
(348, 'F6B2W', 'Caviar', 7),
(349, 'V7TFK', 'Lettuce', 1),
(350, 'V7TFK', 'Yellow Radish', 1),
(351, 'V7TFK', 'Japchae Noodles', 1),
(352, 'V7TFK', 'Onions', 1),
(353, 'V7TFK', 'Pepper', 1),
(354, 'V7TFK', 'French Fries', 1),
(355, 'V7TFK', 'Chicken Meat', 1),
(356, 'V7TFK', 'Bacon', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rpos_staff`
--

CREATE TABLE `rpos_staff` (
  `staff_id` int(20) NOT NULL,
  `staff_name` varchar(200) NOT NULL,
  `staff_number` varchar(200) NOT NULL,
  `staff_email` varchar(200) NOT NULL,
  `staff_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_staff`
--

INSERT INTO `rpos_staff` (`staff_id`, `staff_name`, `staff_number`, `staff_email`, `staff_password`, `created_at`) VALUES
(7, 'kate', 'VOQF-9845', 'kate1@cashier.com', 'kate', '2024-04-02 22:58:44.895370'),
(8, 'khyla', 'VFHR-4801', 'khyla1@cashier.com', 'kahyla12', '2024-04-02 22:59:04.641879'),
(9, 'drix', 'GBNX-0247', 'drix@cashier.com', '2639baf63e6a401e629777788037e6acb64e0e26', '2024-04-23 07:54:40.740544');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rpos_admin`
--
ALTER TABLE `rpos_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `rpos_customers`
--
ALTER TABLE `rpos_customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `rpos_inventory`
--
ALTER TABLE `rpos_inventory`
  ADD PRIMARY KEY (`ingredients_id`);

--
-- Indexes for table `rpos_kiosk_orders`
--
ALTER TABLE `rpos_kiosk_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `CustomerOrder` (`customer_id`) USING BTREE;

--
-- Indexes for table `rpos_orders`
--
ALTER TABLE `rpos_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `CustomerOrder` (`customer_id`),
  ADD KEY `ProductOrder` (`prod_id`);

--
-- Indexes for table `rpos_pass_resets`
--
ALTER TABLE `rpos_pass_resets`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `rpos_payments`
--
ALTER TABLE `rpos_payments`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `order` (`order_code`);

--
-- Indexes for table `rpos_products`
--
ALTER TABLE `rpos_products`
  ADD PRIMARY KEY (`prod_id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `rpos_product_ingredients`
--
ALTER TABLE `rpos_product_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rpos_inventory`
--
ALTER TABLE `rpos_inventory`
  MODIFY `ingredients_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `rpos_kiosk_orders`
--
ALTER TABLE `rpos_kiosk_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `rpos_pass_resets`
--
ALTER TABLE `rpos_pass_resets`
  MODIFY `reset_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpos_product_ingredients`
--
ALTER TABLE `rpos_product_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=357;

--
-- AUTO_INCREMENT for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  MODIFY `staff_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rpos_orders`
--
ALTER TABLE `rpos_orders`
  ADD CONSTRAINT `CustomerOrder` FOREIGN KEY (`customer_id`) REFERENCES `rpos_customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ProductOrder` FOREIGN KEY (`prod_id`) REFERENCES `rpos_products` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rpos_product_ingredients`
--
ALTER TABLE `rpos_product_ingredients`
  ADD CONSTRAINT `fk_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `rpos_products` (`prod_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
