USE siskeudes;

ALTER TABLE bku 
ADD COLUMN jenis_transaksi ENUM('Pendapatan','Belanja','Mutasi') NOT NULL 
AFTER uraian;

DESCRIBE bku;
