Write-Host "Iniciando importacao do Censo..." -ForegroundColor Cyan

Write-Host "`n[1/17] AGOSTINHO_JOSE"
php artisan importar:censo "C:\temp\CSV_Escola\AGOSTINHO_JOSE.csv" 18

Write-Host "`n[2/17] AGOSTINHO_RIBEIRO"
php artisan importar:censo "C:\temp\CSV_Escola\AGOSTINHO_RIBEIRO.csv" 9

Write-Host "`n[3/17] ARLETE_GOIS"
php artisan importar:censo "C:\temp\CSV_Escola\ARLETE_GOIS.csv" 4

Write-Host "`n[4/17] CASSIANO_MARTINS"
php artisan importar:censo "C:\temp\CSV_Escola\CASSIANO_MARTINS.csv" 5

Write-Host "`n[5/17] CORIOLANO"
php artisan importar:censo "C:\temp\CSV_Escola\CORIOLANO.csv" 14

Write-Host "`n[6/17] CRECHE_ANATILDES"
php artisan importar:censo "C:\temp\CSV_Escola\CRECHE_ANATILDES.csv" 19

Write-Host "`n[7/17] D.PEDRO_II"
php artisan importar:censo "C:\temp\CSV_Escola\D.PEDRO_II.csv" 8

Write-Host "`n[8/17] DESCOBRINDO_O_SABER"
php artisan importar:censo "C:\temp\CSV_Escola\DESCOBRINDO_O_SABER.csv" 2

Write-Host "`n[9/17] GETULIO_VARGAS"
php artisan importar:censo "C:\temp\CSV_Escola\GETULIO_VARGAS.csv" 6

Write-Host "`n[10/17] HENRIQUE_BRITO"
php artisan importar:censo "C:\temp\CSV_Escola\HENRIQUE_BRITO.csv" 3

Write-Host "`n[11/17] LUDUGERIO"
php artisan importar:censo "C:\temp\CSV_Escola\LUDUGERIO.csv" 15

Write-Host "`n[12/17] LUIS_VIANA"
php artisan importar:censo "C:\temp\CSV_Escola\LUIS_VIANA.csv" 13

Write-Host "`n[13/17] MANOEL_LOTERIO"
php artisan importar:censo "C:\temp\CSV_Escola\MANOEL_LOTERIO.csv" 12

Write-Host "`n[14/17] MARIA_ANTONIA"
php artisan importar:censo "C:\temp\CSV_Escola\MARIA_ANTONIA.csv" 10

Write-Host "`n[15/17] RIACHUELO"
php artisan importar:censo "C:\temp\CSV_Escola\RIACHUELO.csv" 7

Write-Host "`n[16/17] ROBERTO_SANTOS"
php artisan importar:censo "C:\temp\CSV_Escola\ROBERTO_SANTOS.csv" 1

Write-Host "`n[17/17] RUI_BARBOSA"
php artisan importar:censo "C:\temp\CSV_Escola\RUI_BARBOSA.csv" 11

Write-Host "`nImportacao concluida." -ForegroundColor Green
