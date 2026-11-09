SELECT COUNT(*) as total_students FROM users WHERE role = 'mahasiswa';
SELECT COUNT(*) as students_with_phone FROM users WHERE role = 'mahasiswa' AND phone IS NOT NULL AND phone != '';
SELECT id, name, email, phone FROM users WHERE role = 'mahasiswa' AND (phone IS NULL OR phone = '');
SELECT COUNT(*) as pending_claims FROM claims WHERE status_klaim = 'pending';
SELECT c.id, u.name, u.phone FROM claims c JOIN users u ON c.id_user = u.id WHERE c.status_klaim = 'pending' LIMIT 10;
