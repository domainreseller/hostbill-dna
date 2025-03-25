<div align="center">  
  <a href="README.md"   >   TR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/TR.png" alt="TR" height="20" /></a>  
  <a href="README-EN.md"> | EN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/US.png" alt="EN" height="20" /></a>  
  <a href="README-AZ.md"> | AZ <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AZ.png" alt="AZ" height="20" /></a>  
  <a href="README-DE.md"> | DE <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/DE.png" alt="DE" height="20" /></a>  
  <a href="README-FR.md"> | FR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/FR.png" alt="FR" height="20" /></a>  
  <a href="README-AR.md"> | AR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AR.png" alt="AR" height="20" /></a>  
  <a href="README-CN.md"> | CN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/CN.png" alt="AR" height="20" /></a>  
  <a href="README-NL.md"> | NL <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/NL.png" alt="NL" height="20" /></a>  
</div>


# Genel Baxış

**DomainNameApi**, kiçik və ev əsaslı işlərə, fərdlərə, trafik yığıcılarına və yeniden satıcılara domen adı qeydiyyatı və digər onlayn xidmətlər təqdim edən öndə gələn bir domen adı qeydiyyatçısıdır. HostBill sizə **DomainNameApi** domen təminatı və idarəetməni avtomatlaşdırmağa imkan verir.

## Modulun Aktivləşdirilməsi
İlk olaraq, faylları HostBill direktoriyanıza yükləməlisiniz:

Modulu aktivləşdirmək üçün HostBill admin panelinə daxil olun, Parametrlər → Modullar → Domen Modulları'na keçin, **DomainNameApi** modulunu tapın və Aktivləşdir'e vurun.

![](image.jpg)

## Modul Konfiqurasiyası

Modulu aktivləşdirdikdən sonra modul konfiqurasiya səhifəsinə yönləndiriləcəksiniz. Aktivləşdirilmiş modulu konfiqurasiya etmək üçün Parametrlər → Tətbiqlər → Yeni Tətbiq Əlavə Et'ə də gedə bilərsiniz.

Konfiqurasiya sahələrini doldurun:

- Tətbiqin adı
- İstifadəçi adı
- Şifrə

Daha sonra nameserver'larınızı əlavə etməyə keçin:

- Birinci Nameserver
- Birinci Nameserver IP

HostBill'in bağlana bilməsini yoxlamaq üçün Test Konfiqurasiyasını istifadə edin.

Yeni Tətbiq Əlavə Et'e vurun.

# Domen İmportu

Əlavələr -> İçəri aktar -> Xidmətləri içəri aktar seçin. Domainnameapi modulu siyahıda görünməlidir. Seçin və Davam et düyməsinə basın.
![img_1.png](img_1.png)
Domen adları siyahılanacaq. Müştəri və məhsulları uyğunlaşdırıb içəri aktara bilərsiniz.

![img.png](img.png)

# Domen Qiymətləndirməsi

Əlavələr -> İçəri aktar -> TLD qiymətlərini seçin, Bir profil yaradın.
Diqqətlə dərəcələri və digər parametrləri doldurun.

![img_2.png](img_2.png)

# Domen Sorğu Parametrləri

Bir uzantı üçün sorğu parametrlərini təyin etmək üçün, Parametrlər -> Domen Parametrləri sekmesinə gedin.
![img_3.png](img_3.png)
Uzantını düzəltdiyinizdə Whois mühərriki və Uyğunluq yoxlamasını Domainnameapi olaraq seçin. Bu şəkildə sorğularınız ümumi whois serverlərindən deyil, Domainnameapi vasitəsilə aparılacaq.
Qeyd: bu əməliyyat üçün Hostbill Dizini > includes > extend > whois içindəki whois.custom.php.example faylının adını whois.custom.php olaraq dəyişdirməlisiniz.

![img_4.png](img_4.png)

# TR Domen Qeydiyyat Prosesi üçün Ön Məlumatlar
Parametrlər -> Məhsullar -> Domen məhsulunuzu seçin, *.tr domenini düzəltməyi seçin. Əlavələr sekmesinə gəldiyinizdə əlavə domen sahələri xəbərdarlığını göreceksiniz. Əlavə edin.
![img_5.png](img_5.png)
Dəyişən adı xaricindəki bütün məlumatları istəyinizə görə düzəldin, həmçinin "Təkmilləşdirilmiş" sekmesindəki "Tələb olunur" işarəsini silin (Bunu etməsəniz, müştəri domen qeydiyyatı zamanı bu sahələri doldurmalı olacaq.)
![img_6.png](img_6.png)
