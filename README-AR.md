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


# لمحة عامة

**DomainNameApi** هو مسجل اسم النطاق الرائد الذي يوفر تسجيل اسم النطاق وخدمات أخرى عبر الإنترنت للشركات الصغيرة والمنزلية، والأفراد، ومجمعي الحركة، والموزعين. يتيح لك HostBill أتمتة تجهيز وإدارة النطاق **DomainNameApi**.

## تنشيط الوحدة
أولاً، تحتاج إلى تحميل الملفات إلى دليل HostBill الخاص بك:

لتنشيط الوحدة، قم بتسجيل الدخول إلى لوحة تحكم HostBill الإدارية، انتقل إلى الإعدادات → الوحدات → وحدات النطاق، ابحث واختر وحدة **DomainNameApi** وانقر فوق تنشيط.

![](image.jpg)

## تكوين الوحدة

بمجرد تنشيط الوحدة، سيتم توجيهك إلى صفحة تكوين الوحدة. لتكوين الوحدة المفعلة، يمكنك أيضًا الانتقال إلى الإعدادات → التطبيقات → إضافة تطبيق جديد.

املأ حقول التكوين:

- اسم التطبيق
- اسم المستخدم
- كلمة المرور

ثم انتقل إلى إضافة خادم الأسماء الخاص بك:

- الخادم الأساسي للأسماء
- IP الخادم الأساسي للأسماء

استخدم تكوين الاختبار للتحقق من إمكانية اتصال HostBill.

انقر على إضافة تطبيق جديد.

# استيراد النطاق

انتقل إلى الإضافات -> استيراد -> استيراد الخدمات. يجب أن يظهر وحدة Domainnameapi في القائمة. حدده وانقر على متابعة.
![img_1.png](img_1.png)
سيتم عرض أسماء النطاقات. يمكنك مطابقة العملاء والمنتجات واستيرادها.

![img.png](img.png)

# تسعير النطاقات

انتقل إلى الإضافات -> استيراد -> أسعار TLD، قم بإنشاء ملف تعريف.
املأ الأسعار والإعدادات الأخرى بعناية.

![img_2.png](img_2.png)

# إعدادات استعلام النطاق

لتعيين إعدادات الاستعلام لامتداد، انتقل إلى علامة التبويب الإعدادات -> إعدادات النطاق.
![img_3.png](img_3.png)
عند تعديل الامتداد، اختر Domainnameapi كمحرك Whois وفحص الامتثال. بهذه الطريقة، سيتم إجراء استعلاماتك من خلال Domainnameapi بدلاً من خوادم whois العامة.
ملاحظة: لهذه العملية، تحتاج إلى إعادة تسمية الملف whois.custom.php.example في دليل Hostbill > includes > extend > whois إلى whois.custom.php.

![img_4.png](img_4.png)

# المتطلبات الأساسية لعملية تسجيل نطاق TR
انتقل إلى الإعدادات -> المنتجات -> اختر منتج النطاق الخاص بك، اختر تعديل نطاق *.tr. عندما تصل إلى علامة التبويب الإضافات، سترى تحذيراً حول حقول النطاق الإضافية. أضفها.
![img_5.png](img_5.png)
قم بتعديل جميع المعلومات باستثناء اسم المتغير حسب رغبتك، وقم أيضاً بإزالة علامة "مطلوب" في علامة التبويب "متقدم" (إذا لم تقم بذلك، سيتعين على العملاء ملء هذه الحقول أثناء تسجيل النطاق.)
![img_6.png](img_6.png)
