SELECT p.id, p.biaya_periksa, COALESCE(t.sum_obat,0) AS sum_obat, p.biaya_periksa - COALESCE(t.sum_obat,0) AS jasa_terlihat
FROM periksa p
LEFT JOIN (
  SELECT dp.id_periksa, SUM(o.harga) AS sum_obat
  FROM detail_periksa dp
  LEFT JOIN obat o ON o.id = dp.id_obat
  GROUP BY dp.id_periksa
) t ON t.id_periksa = p.id
ORDER BY p.id;
