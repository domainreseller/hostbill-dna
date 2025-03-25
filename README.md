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


# Genel Bakış

**DomainNameApi**, küçük ve ev tabanlı işletmelere, bireylere, trafik toplayıcılara ve yeniden satıcılara alan adı kaydı ve diğer çevrimiçi hizmetler sağlayan önde gelen bir alan adı kayıt firmasıdır. HostBill, **DomainNameApi** alan adı oluşturma ve yönetimini otomatikleştirmenizi sağlar.

## Modülün Aktifleştirilmesi
Öncelikle dosyaları HostBill dizinine yüklemeniz gerekmektedir:

Modülü aktifleştirmek için HostBill yönetici panelinize giriş yapın, Ayarlar → Modüller → Alan Adı Modülleri'ne gidin, **DomainNameApi** modülünü bulun ve Aktifleştir'e tıklayın.

![](image.jpg)

## Modül Konfigürasyonu

Modülü aktifleştirdikten sonra modül konfigürasyon sayfasına yönlendirilirsiniz. Aktifleştirilen modülü yapılandırmak için Ayarlar → Uygulamalar → Yeni Uygulama Ekle'ye de gidebilirsiniz.

Konfigürasyon alanlarını doldurun:

- Uygulamanın adı
- Kullanıcı adı
- Parola

Daha sonra nameserver'larınızı eklemeye geçin:

- Birincil Nameserver
- Birincil Nameserver IP

HostBill'in bağlanıp bağlanamadığını kontrol etmek için Test Konfigürasyonu'nu kullanın.

Yeni Uygulama Ekle'ye tıklayın.

# Alan adı içe aktarımı

Ekstralar -> İçeri aktar -> Servisleri içeri aktarı seçin. Domainnameapi modülü listede görünmelidir. Seçin ve Devam et butonuna basın.
![img_1.png](img_1.png)
Alan adları listelenecektir.  Müşteri ve ürün eşleştirip içe aktarım yapabilirsiniz

![img.png](img.png)


# Alan adları fiyatlandırması

Ekstralar -> İçeri aktar ->TLD fiyatlarını seçin, Bir profil oluşturun. 
Dikkatlice oranları ve diğer ayarları doldurun.

![img_2.png](img_2.png)


# Alan adı sorgulama ayarları

Bir uzantıya ait sorgulama ayarlarını yapmak için, Ayarlar->Alan adı ayarları sekmesine gidin gidin.
![img_3.png](img_3.png)
Uzantıyı düzelediğinizde Whois motoru ve Uygunluk kontrolünü Domainnameapi olarak seçin. Böylece sorgulamalarınız genel whois sunucularından değil, Domainnameapi üzerinden yapılacaktır.
Not: bu işlem için Hostbill Dizini > includes > extend > whois içindeki whois.custom.php.example dosyasının ismini whois.custom.php olarak değiştirmelisiniz.

![img_4.png](img_4.png)

# TR Alan adı kayıt işlemi için ön bilgiler
Ayarlar -> Ürünler-> Alan adı ürününüzü seçin, *.tr alan adını düzenlemeyş seçin. Eklentiler sekmesine geldiğinizde ek alan adı alanları uyarısı olduğunu göreceksiniz. Ekleyin.
![img_5.png](img_5.png)
Değişken adı haricindeki tüm bilgileri isteğinize göre düzenleyin, ayrıca "Gelişmiş" sekmesinde ki "Gerekli" işaretini kaldırın (Bunu yapmazsanız, müşteri alan adı kayıt işlemi sırasında bu alanları doldurmak zorunda kalacaktır.)
![img_6.png](img_6.png)