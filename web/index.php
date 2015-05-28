<?php
// используется ldap-привязка
$ldaprdn  = 'CN=Барышев Дмитрий Анатольевич,OU=Users,OU=R4S,OU=SVRD-44-B,OU=SPB,OU=RU,OU=Offices,DC=tps,DC=local';     // ldap rdn или dn
$ldappass = 'tanya220964';  // ассоциированный пароль

// соединение с сервером
$ldapconn = ldap_connect("tps.local") or die("NO");
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);


if ($ldapconn) {

    // привязка к ldap-серверу
    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

    // проверка привязки
    if ($ldapbind) {
        echo "good";
    } else {
        echo "fuck";
    }

}
 ?>