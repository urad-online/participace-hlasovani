<?php
/**
 * PB 1.00
 * Creates all used taxonomies
 *
 */

function get_term_definition()
{
  $taxo_vote_cat = array(
      array('title' => "Participativní projekty", 'slug' => 'parti-projekty', 'description' => "", ),
      array('title' => "Charita", 'slug' => 'charita', 'description' => "",),
      array('title' => "Hlasování o složeni rady", 'slug' => 'hlasovani-rada', 'description' => "",),
  );
  $taxo_imc_cat = array(
      array('title' => "Malý projekt", 'slug' => 'maly-projekt', 'description' => "",),
      array('title' => "Velký projekt", 'slug' => 'velky-projekt', 'description' => "",),
      array('title' => "Největší projekt", 'slug' => 'nejvetsi-projekt', 'description' => "",),
  );

  $taxo_vote_status = array(
    array('title' => "V přípravě",       'slug' => 'v-priprave',  'description' => "", 'meta' => array('allow_adding_project' => 0, 'allow_voting'=> 0, 'voting_status_color' => '#2f7cbf'),),
    array('title' => "Sběr návrhů",      'slug' => "sber-navrhu", 'description' => "", 'meta' => array('allow_adding_project' => 1, 'allow_voting'=> 0, 'voting_status_color' => '#31bfc4'),),
    array('title' => "Hodnocení návrhů", 'slug' => "hodnoceni-navrhu", 'description' => "", 'meta' => array('allow_adding_project' => 0, 'allow_voting'=> 0, 'voting_status_color' => '#bf31c6'),),
    array('title' => "Probíhá hlasování",  'slug' => "probiha-hlasovani", 'description' => "", 'meta' => array('allow_adding_project' => 0, 'allow_voting'=> 1, 'voting_status_color' => '#2fbc49'),),
    array('title' => "Hlasování ukončeno", 'slug' => "hlasovani-ukonceno", 'description' => "", 'meta' => array('allow_adding_project' => 0, 'allow_voting'=> 0, 'voting_status_color' => '#dd3333'),),
    array('title' => "Vybrány návrhy",     'slug' => "vybrany-navrhy", 'description' => "", 'meta' => array('allow_adding_project' => 0, 'allow_voting'=> 0, 'voting_status_color' => '#dd3333'),),
  );

  $taxo_imc_status = array(
    array('title' => "Úprava návrhu", 'slug' => "uprava-navrhu",
        'meta' => array('imc_term_order' => 1,),
        'tax_imcstatus_color_' => "#ffeb3b",
        'description' => "Návrh ve stavu \"Úpravy návrhu\" lze ze strany navrhovatele upravovat.  Svůj návrh nalezne na stránce <i>Aktuálně podané návrhy</i> v navigačním menu <i>Participativní rozpočet</i>.
        Rozpracované návrhy jsou v tomto stavu viditelné jen navrhovatelům a koordinátorce participativního rozpočtu Moje stopa a jsou tedy neveřejné. Je to jediný stav, ve kterém může navrhovatel svůj návrh upravovat a i opakovaně předkládat ke kontrole koordinátorce projektu.",
    ),
    array('title' => "Kontrola návrhu", 'slug' => "kontrola-navrhu",
        'meta' => array('imc_term_order' => 2,),
        'tax_imcstatus_color_' => "#ffc107",
        'description' => "Navrhovatel po řádném vyplnění svého návrhu předloží vyplněný formulář k formální kontrole koordinátorce projektu. Ve stavu <i>Kontrola návrhu</i> není návrh zveřejněn a je k dispozici pouze koordinátorce projektu. Koordinátorka může po kontrole návrh zveřejnit a změnit stav na <i>Přijatý návrh</i> nebo požádat o doplnění ze strany navrhovatele a opětovně změnit stav na <i>Úprava návrhu</i>, ve kterém mohou navrhovatelé své návrhy upravovat a doplňovat.",
      ),
    array('title' => "Přijatý návrh", 'slug' => "prijaty-navrh",
        'meta' => array('imc_term_order' => 3,),
        'tax_imcstatus_color_' => "#ff9800",
        'description' => "Návrh ve stavu <i>Přijatý návrh</i> byl formálně zkontrolován koordinátorkou projektu a byl publikován. Návrh je viditelný všem návštěvníkům webového portálu.",
      ),
    array('title' => "Kontrola proveditelnosti", 'slug' => "kontrola-proveditelnosti",
        'meta' => array('imc_term_order' => 4,),
        'tax_imcstatus_color_' => "#cddc39",
        'description' => "Ve stavu<i>Kontrola proveditelnosti</i> probíhá detailní kontrola položek rozpočtu a dochází k ověření, zda je možné v navržené cenové hladině takový návrh realizovat. Rovněž probíhá detailní prověření všech souvislostí s umístěním, požadavky na projektovou dokumentaci, prověření všech majetkových vztahů. K návrhu se vyjadřují jednotlivé odbory úřadu. Pokud je potřeba doplnění od navrhovatelů, koordinátorka projektu Moje stopa změní stav na <i>Úprava návrhu</i>, pokud je vše v pořádku, stav návrhu je změněn na <i>Návrh k hlasování</i>.",
      ),
    array('title' => "Návrh k hlasování", 'slug' => "navrh-k-hlasovani",
        'meta' => array('imc_term_order' => 5,),
        'tax_imcstatus_color_' => "#00bcd4",
        'description' => "Pokud je návrh v tomto stavu, prošel  kontrolou proveditelnosti, byl uznán jako realizovatelný a o návrhu bude hlasováno v závěrečném hlasování.",
      ),
    array('title' => "Ukončené hlasování", 'slug' => "ukoncene-hlasovani",
        'meta' => array('imc_term_order' => 6,),
        'tax_imcstatus_color_' => "#9c27b0",
        'description' => "Pokud v hlasování o návrzích konkrétní návrh neuspěl a nedostal se svými počty hlasů mezi vítězné, tedy v budoucnu realizované návrhy, zůstává v tomto stavu. Návrh zůstává nadále zveřejněn a je viditelný všem návštěvníkům webového portálu. Detailní informace rovněž zobrazují výsledky dosažené ve veřejném hlasování.",
    ),
    array('title' => "K realizaci", 'slug' => "k-realizaci",
        'meta' => array('imc_term_order' => 7,),
        'tax_imcstatus_color_' => "#009688",
        'description' => "Ve stavu <i>K realizaci</i> zůstávají všechny projekty, které uspěly v závěrečném veřejném hlasování občanů.",
    ),
    array('title' => "Návrh vyřazen", 'slug' => "navrh-vyrazen",
        'meta' => array('imc_term_order' => 8,),
        'tax_imcstatus_color_' => "#f44336",
        'description' => "Ve stavu <i>Návrh vyřazen</i> zůstávají vyřazené návrhy. Jsou publikované a veřejně dostupné všem návštěvníkům webového portálu včetně celé historie a tedy i důvodů jejich vyřazení. Mezi důvody, proč může být návrh vyřazen patří zejména nesplnění schválených pravidel projektu Moje stopa, nedoplnění požadovaných informací ze strany navrhovatelů ve stanoveném termínu, nedostatky ve zpracovaném rozpočtu nebo důvody nerealizovatelnosti projektu. Pokud navrhovatel mladší 18 let nedoloží souhlas s podáním projektu, resp. pokud byl souhlas odepřen, návrh projektu nebude vůbec zveřejněn a veškeré informace budou ze serverů webového portálu smazány. Tyto návrhy tedy nebudou nikdy zveřejněny.",
    ),
  );
  return array(
    'voting_status' => $taxo_vote_status,
    'voting_category' => $taxo_vote_cat,
    'imcstatus' => $taxo_imc_status,
    'imccategory' => $taxo_imc_cat,
  );
}
function pbvote_create_terms($arg = "")
{
    $all_terms = get_term_definition();

    pbvote_create_term( $all_terms['voting_status'], 'voting_status');
    pbvote_create_term( $all_terms['voting_category'], 'voting_category');
    // pbvote_create_term( $all_terms['imcstatus'], 'imcstatus');
    pbvote_create_term( $all_terms['imccategory'], 'imccategory');

}
  function pbvote_create_term( $terms, $taxo)
  {
      foreach ($terms as $term) {
          $term_id = wp_insert_term( $term['title'], $taxo, array('slug'=>$term['slug']) );
          if ((!is_wp_error($term_id)) && (!empty($term['meta']))) {
              foreach ($term['meta'] as $key => $value) {
                update_term_meta( $term_id['term_id'], $key, $value );
              }
              if (!empty($term['tax_imcstatus_color_'])) {
                pbvote_create_terms_color_option( $term_id['term_id'], 'tax_imcstatus_color_', $term['tax_imcstatus_color_']  );
              }
          }
      }

  }
function pbvote_create_terms_color_option( $term_id, $option_prefix, $value)
{
  $option_id = $option_prefix . $term_id;
  $option_old = get_option($option_id);

}
