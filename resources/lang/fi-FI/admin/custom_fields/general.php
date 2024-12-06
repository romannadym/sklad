<?php

return [
    'custom_fields'		        => 'Mukautetut kentät',
    'manage'                    => 'Hallitse',
    'field'		                => 'Kenttä',
    'about_fieldsets_title'		=> 'Tietoja kenttäsarjoista',
    'about_fieldsets_text'		=> 'Kenttäkokoelma mahdollistaa kokoelmien muodostamisen mukautetuista kentistä joita käytetään usein tiettyjen laitemallien kanssa.',
    'custom_format'             => 'Mukautettu regex-formaatti...',
    'encrypt_field'      	        => 'Salaa tämän kentän arvo tietokannassa',
    'encrypt_field_help'      => 'VAROITUS: Kentän salaaminen estää kentän arvolla hakemisen.',
    'encrypted'      	        => 'Salattu',
    'fieldset'      	        => 'Kenttäsarja',
    'qty_fields'      	      => 'Kenttien määrä',
    'fieldsets'      	        => 'Kenttäsarjat',
    'fieldset_name'           => 'Kenttäsarjan nimi',
    'field_name'              => 'Kentän nimi',
    'field_values'            => 'Kentän arvot',
    'field_values_help'       => 'Lisää vaihtoehtoehtoja yksi per rivi. Muut tyhjät rivit, paitsi ensimmäinen, jätetään huomiotta.',
    'field_element'           => 'Lomakkeen elementti',
    'field_element_short'     => 'Elementti',
    'field_format'            => 'Muoto',
    'field_custom_format'     => 'Mukautettu regex-muoto',
    'field_custom_format_help'     => 'Tämän kentän avulla voit käyttää regex-lauseketta validointiin. Sen pitäisi alkaa "regex:" - esimerkiksi vahvistaaksesi, että mukautetun kentän arvo sisältää voimassa olevan IMEI: n (15 numeerista numeroa), käyttäisit <code>regex: /^[0-9]{15}$/</code>.',
    'required'   		          => 'Vaadittu',
    'req'   		              => 'Vaad.',
    'used_by_models'   		    => 'Käytetään malleissa',
    'order'   		            => 'Tilata',
    'create_fieldset'         => 'Uusi kenttäsarja',
    'update_fieldset'         => 'Päivitä kenttäkokoelma',
    'fieldset_does_not_exist'   => 'Kenttäkokoelmaa :id ei ole olemassa',
    'fieldset_updated'         => 'Kenttäkokoelma päivitetty',
    'create_fieldset_title' => 'Luo uusi kenttäkokoelma',
    'create_field'            => 'Uusi mukautettu kenttä',
    'create_field_title' => 'Luo uusi mukautettu kenttä',
    'value_encrypted'      	        => 'Kentän arvo salataan tietokannassa. Vain järjestelmänvalvojat voivat tarkastella purettua arvoa',
    'show_in_email'     => 'Käytetäänkö kentän arvoa käyttäjälle lähetettävissä luovutus-sähköposteissa? Salattuja kenttiä ei voi lisätä sähköposteihin',
    'show_in_email_short'     => 'Sisällytä sähköposteihin.',
    'help_text' => 'Aputeksti',
    'help_text_description' => 'Tämä on valinnainen teksti joka ilmestyy lomakekentän alapuolelle laitetta muokatessa tarjotakseen kontekstia kentälle.',
    'about_custom_fields_title' => 'Mukautetuista kentistä',
    'about_custom_fields_text' => 'Mukautetut kentät mahdollistavat mielivaltaisten ominaisuuksien lisäämisen laitteille.',
    'add_field_to_fieldset' => 'Lisää kenttä kenttäkokoelmaan',
    'make_optional' => 'Pakollinen - klikkaa tehdäksesi valinnaiseksi',
    'make_required' => 'Valinnainen - klikkaa tehdäksesi pakolliseksi',
    'reorder' => 'Järjestä',
    'db_field' => 'Tietokantakenttä',
    'db_convert_warning' => 'VAROITUS. Tämä kenttä on mukautettujen kenttien tietokantataulussa <code>:db_column</code>, mutta sen pitäisi olla <code>:expected</code>.',
    'is_unique' => 'Tämän arvon täytyy olla yksilöllinen kaikille laitteille',
    'unique' => 'Yksilöllinen',
    'display_in_user_view' => 'Salli laitteen lainanneen käyttäjän nähdä nämä arvot heidän lainattujen laitteiden sivulla',
    'display_in_user_view_table' => 'Näkyvä käyttäjälle',
    'auto_add_to_fieldsets' => 'Lisää tämä automaattisesti kaikkiin uusiin kenttäkokoelmiin',
    'add_to_preexisting_fieldsets' => 'Lisää olemassaoleviin kenttäkokoelmiin',
    'show_in_listview' => 'Näytä oletusarvoisesti listan näkymissä. Valtuutetut käyttäjät voivat silti näyttää tai piilottaa sarakkeen valitsimen kautta',
    'show_in_listview_short' => 'Näytä listoissa',
    'show_in_requestable_list_short' => 'Näytä pyydettävien assettien luettelossa',
    'show_in_requestable_list' => 'Näytä arvo pyydettävissä olevien assettien luettelossa. Salattuja kenttiä ei näytetä',
    'encrypted_options' => 'Tämä kenttä on salattu, joten jotkin näyttöasetukset eivät ole käytettävissä.',

];
