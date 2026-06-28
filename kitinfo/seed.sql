-- =====================================================================
-- kitinfo.ru — тестовые данные
-- Пароль администратора по умолчанию: admin123 (СМЕНИТЬ после первого входа!)
-- =====================================================================

USE kitinfo;

INSERT INTO organizations (name, inn, phone, email, address) VALUES
('ООО «Ромашка»', '7701234567', '+7 495 000-00-01', 'info@romashka.test', 'г. Москва, ул. Примерная, 1'),
('ИП Кузнецов А.В.', '770123456789', '+7 495 000-00-02', 'kuznetsov@test.ru', 'г. Москва, ул. Тестовая, 5');

-- Пароль: admin123 (bcrypt-хеш, совместим с password_hash()/password_verify() в PHP)
INSERT INTO users (organization_id, full_name, email, phone, password_hash, role, is_active) VALUES
(NULL, 'Администратор системы', 'admin@kitinfo.ru', '+7 000 000-00-00',
 '$2b$12$qmyrt04sVILuq0d3w41UIOQX.HrbERePvGLh/sIClcyfm/NRjSptK', 'admin', 1),
(1, 'Иван Петров', 'ivan@romashka.test', '+7 495 000-00-03',
 '$2b$12$qmyrt04sVILuq0d3w41UIOQX.HrbERePvGLh/sIClcyfm/NRjSptK', 'user', 1);

INSERT INTO services (code, title, category, description, price_from, sort_order) VALUES
('1C-SUPPORT',    '1С: настройка и поддержка', '1c',
 'Установка, обновление и доработка конфигураций, исправление ошибок, консультации для бухгалтеров и руководителей.', 3000.00, 1),
('OUTSOURCE',     'ИТ-аутсорсинг', 'outsource',
 'Комплексное обслуживание ИТ-инфраструктуры компании на абонентской основе.', 15000.00, 2),
('SERVER-LEASE',  'Аренда серверов', 'server',
 'Подбор, размещение и администрирование серверного оборудования, резервное копирование, мониторинг 24/7.', 5000.00, 3),
('KKM-SERVICE',   'Ремонт и контроль ККТ', 'kkm',
 'Ремонт, настройка и регистрация касс в ФНС, замена фискального накопителя, плановый контроль работы.', 2000.00, 4);

INSERT INTO requests (user_id, organization_id, service_id, guest_name, guest_phone, guest_email, message, status) VALUES
(2, 1, 1, NULL, NULL, NULL, 'Нужна консультация по обновлению конфигурации 1С:Бухгалтерия.', 'new'),
(NULL, NULL, 4, 'Светлана Орлова', '+7 495 111-22-33', 'orlova@test.ru', 'Касса не печатает чек, нужен мастер.', 'in_progress');
