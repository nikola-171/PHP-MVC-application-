create database if not exists drustvena_odgovornost;
use drustvena_odgovornost;

create table if not exists korisnici(
	id int unsigned not null auto_increment,
    ime varchar(20) not null,
    prezime varchar(20) not null,
    godina_rodjenja varchar(4) not null,
    mesec_rodjenja varchar(2) not null,
    dan_rodjenja varchar(2) not null,
    korisnicko_ime varchar(30) not null unique,
	email varchar(60) not null,
    lozinka varchar(250) not null,
    index(id),
    index(godina_rodjenja),
	index(mesec_rodjenja), 
	index(dan_rodjenja),
    primary key(id)
)engine=InnoDB;

create table if not exists preduzeca(
	id int unsigned not null auto_increment,
    naziv varchar(40) not null unique,
    lozinka varchar(250) not null,
    index(id),
    maticni_broj varchar(8) not null unique,
    sifra_privrednog_drustva varchar(8) not null,
    email varchar(60) not null,
    primary key(id)
)engine=InnoDB;

create table if not exists objava(
	id int unsigned not null auto_increment,
    preduzece int unsigned not null,
    datum date not null,
    datum_postavljanja datetime not null,
    putanja_slike varchar(45) default null,
    orijentacija varchar(1) default null,
    index(datum),
    tekst varchar(350) not null,
    naslov varchar(150) not null,
    primary key(id),
    foreign key(preduzece) references preduzeca(id)
    on delete cascade
)engine=InnoDB;

create table reset_lozinke_fizicko_lice(
    korisnik int not null,
    token varchar(255) not null,
    vreme varchar(50) not null,
    broj_promena int not null default 0,
    primary key(korisnik)
)engine=InnoDB;

create table reset_lozinke_preduzece(
    preduzece int not null,
    token varchar(255) not null,
    vreme varchar(50) not null,
    primary key(preduzece)
)engine=InnoDB;

create table if not exists administrator(
    ime varchar(20) not null,
    prezime varchar(20) not null,
    administrator_ime varchar(30) not null unique,
	email varchar(60) not null,
    lozinka varchar(250) not null,
    primary key(administrator_ime)
)engine=InnoDB;

create table if not exists Pretraga(
	id int unsigned auto_increment,
    sadrzaj varchar(500) not null,
    naslov varchar(25) not null,
    link varchar(50) not null,
    primary key(id)
)engine=InnoDB;

insert into Pretraga(sadrzaj,naslov,link)
values('pocetna strana main page loby home','pocetna_strana','/');

insert into Pretraga(sadrzaj,naslov,link)
values('registracija register ','registracija','/registracija');

insert into Pretraga(sadrzaj,naslov,link)
values('logovanje uloguj se ulogujte se login ','logovanje','/logovanje');

insert into Pretraga(sadrzaj,naslov,link)
values('info informacije društvena odgovornost socialy responsible companies','info','/info');

insert into Pretraga(sadrzaj,naslov,link)
values('administrator aplikacije admin administrator registracija logovanje administratora ','administrator','/administrator_logovanje');



