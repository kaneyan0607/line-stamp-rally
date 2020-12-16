-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:8889
-- 生成日時: 2020 年 12 月 16 日 09:53
-- サーバのバージョン： 5.7.30
-- PHP のバージョン: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- データベース: `shiseidou2`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `line_id` int(11) NOT NULL,
  `line_name` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`line_id`);



-- テーブルの構造 `stamp_results`
--

CREATE TABLE `stamp_results` (
  `id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `stamp_results`
--
ALTER TABLE `stamp_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `line_id` (`line_id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `stamp_results`
--
ALTER TABLE `stamp_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `stamp_results`
--
ALTER TABLE `stamp_results`
  ADD CONSTRAINT `stamp_results_ibfk_1` FOREIGN KEY (`line_id`) REFERENCES `users` (`line_id`);
