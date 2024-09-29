<?php
//FINDONE le pasas el id y te lo busca
 $usuario = ORM::for_table('users')->find_one(1);
 print($usuario->id);
 $usuario->name_user='golfas';
 $usuario->save();

 $usuario = ORM::for_table('users')->where('email_user','soyconcha@entro.com')->find_one();
 print($usuario->email_user);
// ['email_user'->'asdfaef', 'name_user'->'asdefae'] esto valdría para hacer un where and mas condiciones
 $usuarios = ORM::for_table('users')->where_like('email_user','%@%')->find_many();


 foreach($usuarios as $usuario){
    print($usuario->email_user);
 }
//  $comunidades = ORM::for_table('comunidades')->find_many();

$usuarios = ORM::for_table('users')->select_many('name_user','email_user','type_user')->where_like('email_user','%@%')->find_many();
foreach($usuarios as $usuario){
    print($usuario->email_user);
    print($usuario->name_user);
    print($usuario->type_user);
 }

 $newUser=ORM::for_table('users')->create();
 $newUser->email_user='hola@hola.com';
 $newUser->name_user='hola';
 $newUser->save();

$usuario = ORM::for_table('users')->find_one(4)->delete();//hacer esta parte si el find encuentra y si lo encuentra hacer el delete para evitar el error
var_dump($usuario);