<?php

/**
 * Copyright (c) 2009-2013 Pavel Sedlák
 *
 * Kód této html stránky je svobodný software; můžete jej šířit a upravovat
 * v souladu s podmínkami GNU Lesser General Public License verze 2.1,
 * tak jak ji vydala nadace Free Software Foundation;
 *
 * Tento kód je šířen v naději, že bude užitečný, avšak
 * BEZ JAKÉKOLI ZÁRUKY; dokonce i bez předpokládané záruky
 * OBCHODOVATELNOSTI či VHODNOSTI PRO URČITÝ ÚČEL.
 * Další podrobnosti viz. GNU Lesser General Public License.
 *
 * Spolu s tímto kódem jste měli obdržet kopii GNU Lesser General
 * Public License; pokud se tak nestalo, pište na Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @url: http://www.pteryx.net/sklonovani.html
 *
 * Usage:
 * $x = new Inflection();
 * $tvary = $x->inflect('nejaky text'[, $zivtone=false[, $preferovanyRod='']]);
 *
 */
class Inflection
{
    /**
     * Definition of genus (ženský, mužský, střední)
     */
    const GENUS_FEMININE = 'ž';
    const GENUS_MASCULINE = 'm';
    const GENUS_NEUTER = 's';

    private $isDebugMode = false;

    // Ve zvl. pripadech je mozne pomoci teto promenne "pretypovat" rod jmena
    protected $PrefRod = "0"; // smi byt "0", "m", "ž", "s"

    /**
     * Přídavná jména a zájmena
     */

    protected $vzor = array(
        // Přídavná jména a zájmena
        array("m", "-ký", "kého", "kému", "ký/kého", "ký", "kém", "kým", "-ké/-cí", "kých", "kým", "ké", "-ké/-cí", "kých", "kými")
    , array("m", "-rý", "rého", "rému", "rý/rého", "rý", "rém", "rým", "-ré/-ří", "rých", "rým", "ré", "-ré/-ří", "rých", "rými")
    , array("m", "-chý", "chého", "chému", "chý/chého", "chý", "chém", "chým", "-ché/-ší", "chých", "chým", "ché", "-ché/-ší", "chých", "chými")
    , array("m", "-hý", "hého", "hému", "hý/hého", "hý", "hém", "hým", "-hé/-zí", "hých", "hým", "hé", "-hé/-zí", "hých", "hými")
    , array("m", "-ý", "ého", "ému", "ý/ého", "ý", "ém", "ým", "-é/-í", "ých", "ým", "é", "-é/-í", "ých", "ými")
    , array("m", "-[aeěií]cí", "0cího", "0címu", "0cí/0cího", "0cí", "0cím", "0cím", "0cí", "0cích", "0cím", "0cí", "0cí", "0cích", "0cími")
    , array("ž", "-[aeěií]cí", "0cí", "0cí", "0cí", "0cí", "0cí", "0cí", "0cí", "0cích", "0cím", "0cí", "0cí", "0cích", "0cími")
    , array("s", "-[aeěií]cí", "0cího", "0címu", "0cí/0cího", "0cí", "0cím", "0cím", "0cí", "0cích", "0cím", "0cí", "0cí", "0cích", "0cími")
    , array("m", "-[bcčdhklmnprsštvzž]ní", "0ního", "0nímu", "0ní/0ního", "0ní", "0ním", "0ním", "0ní", "0ních", "0ním", "0ní", "0ní", "0ních", "0ními")
    , array("ž", "-[bcčdhklmnprsštvzž]ní", "0ní", "0ní", "0ní", "0ní", "0ní", "0ní", "0ní", "0ních", "0ním", "0ní", "0ní", "0ních", "0ními")
    , array("s", "-[bcčdhklmnprsštvzž]ní", "0ního", "0nímu", "0ní/0ního", "0ní", "0ním", "0ním", "0ní", "0ních", "0ním", "0ní", "0ní", "0ních", "0ními")

    , array("m", "-[i]tel", "0tele", "0teli", "0tele", "0tel", "0teli", "0telem", "0telé", "0telů", "0telům", "0tele", "0telé", "0telích", "0teli")
    , array("m", "-[í]tel", "0tele", "0teli", "0tele", "0tel", "0teli", "0telem", "átelé", "áteli", "átelům", "átele", "átelé", "átelích", "áteli")

    , array("s", "-é", "ého", "ému", "é", "é", "ém", "ým", "-á", "ých", "ým", "á", "á", "ých", "ými")
    , array("ž", "-á", "é", "é", "ou", "á", "é", "ou", "-é", "ých", "ým", "é", "é", "ých", "ými")
    , array("-", "já", "mne", "mně", "mne/mě", "já", "mně", "mnou", "my", "nás", "nám", "nás", "my", "nás", "námi")
    , array("-", "ty", "tebe", "tobě", "tě/tebe", "ty", "tobě", "tebou", "vy", "vás", "vám", "vás", "vy", "vás", "vámi")
    , array("-", "my", "", "", "", "", "", "", "my", "nás", "nám", "nás", "my", "nás", "námi")
    , array("-", "vy", "", "", "", "", "", "", "vy", "vás", "vám", "vás", "vy", "vás", "vámi")
    , array("m", "on", "něho", "mu/jemu/němu", "ho/jej", "on", "něm", "ním", "oni", "nich", "nim", "je", "oni", "nich", "jimi/nimi")
    , array("m", "oni", "", "", "", "", "", "", "oni", "nich", "nim", "je", "oni", "nich", "jimi/nimi")
    , array("ž", "ony", "", "", "", "", "", "", "ony", "nich", "nim", "je", "ony", "nich", "jimi/nimi")
    , array("s", "ono", "něho", "mu/jemu/němu", "ho/jej", "ono", "něm", "ním", "ona", "nich", "nim", "je", "ony", "nich", "jimi/nimi")
    , array("ž", "ona", "ní", "ní", "ji", "ona", "ní", "ní", "ony", "nich", "nim", "je", "ony", "nich", "jimi/nimi")
    , array("m", "ten", "toho", "tomu", "toho", "ten", "tom", "tím", "ti", "těch", "těm", "ty", "ti", "těch", "těmi")
    , array("ž", "ta", "té", "té", "tu", "ta", "té", "tou", "ty", "těch", "těm", "ty", "ty", "těch", "těmi")
    , array("s", "to", "toho", "tomu", "toho", "to", "tom", "tím", "ta", "těch", "těm", "ta", "ta", "těch", "těmi")

        // přivlastňovací zájmena
    , array("m", "můj", "mého", "mému", "mého", "můj", "mém", "mým", "mí", "mých", "mým", "mé", "mí", "mých", "mými")
    , array("ž", "má", "mé", "mé", "mou", "má", "mé", "mou", "mé", "mých", "mým", "mé", "mé", "mých", "mými")
    , array("ž", "moje", "mé", "mé", "mou", "má", "mé", "mou", "moje", "mých", "mým", "mé", "mé", "mých", "mými")
    , array("s", "mé", "mého", "mému", "mé", "moje", "mém", "mým", "mé", "mých", "mým", "má", "má", "mých", "mými")
    , array("s", "moje", "mého", "mému", "moje", "moje", "mém", "mým", "moje", "mých", "mým", "má", "má", "mých", "mými")

    , array("m", "tvůj", "tvého", "tvému", "tvého", "tvůj", "tvém", "tvým", "tví", "tvých", "tvým", "tvé", "tví", "tvých", "tvými")
    , array("ž", "tvá", "tvé", "tvé", "tvou", "tvá", "tvé", "tvou", "tvé", "tvých", "tvým", "tvé", "tvé", "tvých", "tvými")
    , array("ž", "tvoje", "tvé", "tvé", "tvou", "tvá", "tvé", "tvou", "tvé", "tvých", "tvým", "tvé", "tvé", "tvých", "tvými")
    , array("s", "tvé", "tvého", "tvému", "tvého", "tvůj", "tvém", "tvým", "tvá", "tvých", "tvým", "tvé", "tvá", "tvých", "tvými")
    , array("s", "tvoje", "tvého", "tvému", "tvého", "tvůj", "tvém", "tvým", "tvá", "tvých", "tvým", "tvé", "tvá", "tvých", "tvými")

    , array("m", "náš", "našeho", "našemu", "našeho", "náš", "našem", "našim", "naši", "našich", "našim", "naše", "naši", "našich", "našimi")
    , array("ž", "naše", "naší", "naší", "naši", "naše", "naší", "naší", "naše", "našich", "našim", "naše", "naše", "našich", "našimi")
    , array("s", "naše", "našeho", "našemu", "našeho", "naše", "našem", "našim", "naše", "našich", "našim", "naše", "naše", "našich", "našimi")

    , array("m", "váš", "vašeho", "vašemu", "vašeho", "váš", "vašem", "vašim", "vaši", "vašich", "vašim", "vaše", "vaši", "vašich", "vašimi")
    , array("ž", "vaše", "vaší", "vaší", "vaši", "vaše", "vaší", "vaší", "vaše", "vašich", "vašim", "vaše", "vaše", "vašich", "vašimi")
    , array("s", "vaše", "vašeho", "vašemu", "vašeho", "vaše", "vašem", "vašim", "vaše", "vašich", "vašim", "vaše", "vaše", "vašich", "vašimi")

    , array("m", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho")
    , array("ž", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho")
    , array("s", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho")

    , array("m", "její", "jejího", "jejímu", "jejího", "její", "jejím", "jejím", "její", "jejích", "jejím", "její", "její", "jejích", "jejími")
    , array("s", "její", "jejího", "jejímu", "jejího", "její", "jejím", "jejím", "její", "jejích", "jejím", "její", "její", "jejích", "jejími")
    , array("ž", "její", "její", "její", "její", "její", "její", "její", "její", "jejích", "jejím", "její", "její", "jejích", "jejími")

    , array("m", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich")
    , array("s", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich")
    , array("ž", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich")

        // výjimky (zvl. běžná slova)
    , array("m", "-bůh", "boha", "bohu", "boha", "bože", "bohovi", "bohem", "bozi/bohové", "bohů", "bohům", "bohy", "bozi/bohové", "bozích", "bohy")
    , array("m", "-pan", "pana", "panu", "pana", "pane", "panu", "panem", "páni/pánové", "pánů", "pánům", "pány", "páni/pánové", "pánech", "pány")
    , array("s", "moře", "moře", "moři", "moře", "moře", "moři", "mořem", "moře", "moří", "mořím", "moře", "moře", "mořích", "moři")
    , array("-", "dveře", "", "", "", "", "", "", "dveře", "dveří", "dveřím", "dveře", "dveře", "dveřích", "dveřmi")
    , array("-", "housle", "", "", "", "", "", "", "housle", "houslí", "houslím", "housle", "housle", "houslích", "houslemi")
    , array("-", "šle", "", "", "", "", "", "", "šle", "šlí", "šlím", "šle", "šle", "šlích", "šlemi")
    , array("-", "muka", "", "", "", "", "", "", "muka", "muk", "mukám", "muka", "muka", "mukách", "mukami")
    , array("s", "ovoce", "ovoce", "ovoci", "ovoce", "ovoce", "ovoci", "ovocem", "", "", "", "", "", "", "")
    , array("m", "humus", "humusu", "humusu", "humus", "humuse", "humusu", "humusem", "humusy", "humusů", "humusům", "humusy", "humusy", "humusech", "humusy")
    , array("m", "-vztek", "vzteku", "vzteku", "vztek", "vzteku", "vzteku", "vztekem", "vzteky", "vzteků", "vztekům", "vzteky", "vzteky", "vztecích", "vzteky")
    , array("m", "-dotek", "doteku", "doteku", "dotek", "doteku", "doteku", "dotekem", "doteky", "doteků", "dotekům", "doteky", "doteky", "dotecích", "doteky")
    , array("ž", "-hra", "hry", "hře", "hru", "hro", "hře", "hrou", "hry", "her", "hrám", "hry", "hry", "hrách", "hrami")
    , array("m", "zeus", "dia", "diovi", "dia", "die", "diovi", "diem", "diové", "diů", "diům", null, "diové", null, null)
    , array("ž", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol", "nikol")

        // číslovky
    , array("-", "-tdva", "tidvou", "tidvoum", "tdva", "tdva", "tidvou", "tidvěmi", null, null, null, null, null, null, null)
    , array("-", "-tdvě", "tidvou", "tidvěma", "tdva", "tdva", "tidvou", "tidvěmi", null, null, null, null, null, null, null)
    , array("-", "-ttři", "titří", "titřem", "ttři", "ttři", "titřech", "titřemi", null, null, null, null, null, null, null)
    , array("-", "-tčtyři", "tičtyřech", "tičtyřem", "tčtyři", "tčtyři", "tičtyřech", "tičtyřmi", null, null, null, null, null, null, null)
    , array("-", "-tpět", "tipěti", "tipěti", "tpět", "tpět", "tipěti", "tipěti", null, null, null, null, null, null, null)
    , array("-", "-tšest", "tišesti", "tišesti", "tšest", "tšest", "tišesti", "tišesti", null, null, null, null, null, null, null)
    , array("-", "-tsedm", "tisedmi", "tisedmi", "tsedm", "tsedm", "tisedmi", "tisedmi", null, null, null, null, null, null, null)
    , array("-", "-tosm", "tiosmi", "tiosmi", "tosm", "tosm", "tiosmi", "tiosmi", null, null, null, null, null, null, null)
    , array("-", "-tdevět", "tidevíti", "tidevíti", "tdevět", "tdevět", "tidevíti", "tidevíti", null, null, null, null, null, null, null)

    , array("ž", "-jedna", "jedné", "jedné", "jednu", "jedno", "jedné", "jednou", null, null, null, null, null, null, null)
    , array("m", "-jeden", "jednoho", "jednomu", "jednoho", "jeden", "jednom", "jedním", null, null, null, null, null, null, null)
    , array("s", "-jedno", "jednoho", "jednomu", "jednoho", "jedno", "jednom", "jedním", null, null, null, null, null, null, null)
    , array("-", "-dva", "dvou", "dvoum", "dva", "dva", "dvou", "dvěmi", null, null, null, null, null, null, null)
    , array("-", "-dvě", "dvou", "dvoum", "dva", "dva", "dvou", "dvěmi", null, null, null, null, null, null, null)
    , array("-", "-tři", "tří", "třem", "tři", "tři", "třech", "třemi", null, null, null, null, null, null, null)
    , array("-", "-čtyři", "čtyřech", "čtyřem", "čtyři", "čtyři", "čtyřech", "čtyřmi", null, null, null, null, null, null, null)
    , array("-", "-pět", "pěti", "pěti", "pět", "pět", "pěti", "pěti", null, null, null, null, null, null, null)
    , array("-", "-šest", "šesti", "šesti", "šest", "šest", "šesti", "šesti", null, null, null, null, null, null, null)
    , array("-", "-sedm", "sedmi", "sedmi", "sedm", "sedm", "sedmi", "sedmi", null, null, null, null, null, null, null)
    , array("-", "-osm", "osmi", "osmi", "osm", "osm", "osmi", "osmi", null, null, null, null, null, null, null)
    , array("-", "-devět", "devíti", "devíti", "devět", "devět", "devíti", "devíti", null, null, null, null, null, null, null)

    , array("-", "deset", "deseti", "deseti", "deset", "deset", "deseti", "deseti", null, null, null, null, null, null, null)
    , array("-", "-ná[cs]t", "ná0ti", "ná0ti", "ná0t", "náct", "ná0ti", "ná0ti", null, null, null, null, null, null, null)

    , array("-", "-dvacet", "dvaceti", "dvaceti", "dvacet", "dvacet", "dvaceti", "dvaceti", null, null, null, null, null, null, null)
    , array("-", "-třicet", "třiceti", "třiceti", "třicet", "třicet", "třiceti", "třiceti", null, null, null, null, null, null, null)
    , array("-", "-čtyřicet", "čtyřiceti", "čtyřiceti", "čtyřicet", "čtyřicet", "čtyřiceti", "čtyřiceti", null, null, null, null, null, null, null)
    , array("-", "-desát", "desáti", "desáti", "desát", "desát", "desáti", "desáti", null, null, null, null, null, null, null)


        //
        // Spec. přídady skloňování(+předseda, srdce jako úplná výjimka)
        //
    , array("m", "-[i]sta", "0sty", "0stovi", "0stu", "0sto", "0stovi", "0stou", "-0sté", "0stů", "0stům", "0sty", "0sté", "0stech", "0sty")
    , array("m", "-[o]sta", "0sty", "0stovi", "0stu", "0sto", "0stovi", "0stou", "-0stové", "0stů", "0stům", "0sty", "0sté", "0stech", "0sty")
    , array("m", "-předseda", "předsedy", "předsedovi", "předsedu", "předsedo", "předsedovi", "předsedou", "předsedové", "předsedů", "předsedům", "předsedy", "předsedové", "předsedech", "předsedy")
    , array("m", "-srdce", "srdce", "srdi", "sdrce", "srdce", "srdci", "srdcem", "srdce", "srdcí", "srdcím", "srdce", "srdce", "srdcích", "srdcemi")
    , array("m", "-[db]ce", "0ce", "0ci", "0ce", "0če", "0ci", "0cem", "0ci/0cové", "0ců", "0cům", "0ce", "0ci/0cové", "0cích", "0ci")
    , array("m", "-[jň]ev", "0evu", "0evu", "0ev", "0eve", "0evu", "0evem", "-0evy", "0evů", "0evům", "0evy", "0evy", "0evech", "0evy")
    , array("m", "-[lř]ev", "0evu/0va", "0evu/0vovi", "0ev/0va", "0eve/0ve", "0evu/0vovi", "0evem/0vem", "-0evy/0vové", "0evů/0vů", "0evům/0vům", "0evy/0vy", "0evy/0vové", "0evech/0vech", "0evy/0vy")

    , array("m", "-ů[lz]", "o0u/o0a", "o0u/o0ovi", "ů0/o0a", "o0e", "o0u", "o0em", "o-0y/o-0ové", "o0ů", "o0ům", "o0y", "o0y/o0ové", "o0ech", "o0y")

        // výj. nůž ($this->vzor muž)
    , array("m", "nůž", "nože", "noži", "nůž", "noži", "noži", "nožem", "nože", "nožů", "nožům", "nože", "nože", "nožích", "noži")

        //
        // $this->vzor kolo
        //
    , array("s", "-[bcčdghksštvzž]lo", "0la", "0lu", "0lo", "0lo", "0lu", "0lem", "-0la", "0el", "0lům", "0la", "0la", "0lech", "0ly")
    , array("s", "-[bcčdnsštvzž]ko", "0ka", "0ku", "0ko", "0ko", "0ku", "0kem", "-0ka", "0ek", "0kům", "0ka", "0ka", "0cích/0kách", "0ky")
    , array("s", "-[bcčdksštvzž]no", "0na", "0nu", "0no", "0no", "0nu", "0nem", "-0na", "0en", "0nům", "0na", "0na", "0nech/0nách", "0ny")
    , array("s", "-o", "a", "u", "o", "o", "u", "em", "-a", "", "ům", "a", "a", "ech", "y")

        //
        // $this->vzor stavení
        //
    , array("s", "-í", "í", "í", "í", "í", "í", "ím", "-í", "í", "ím", "í", "í", "ích", "ími")
        //
        // $this->vzor děvče  (če,dě,tě,ně,pě) výj.-také sele
        //
    , array("s", "-[čďť][e]", "10te", "10ti", "10", "10", "10ti", "10tem", "1-ata", "1at", "1atům", "1ata", "1ata", "1atech", "1aty")
    , array("s", "-[pb][ě]", "10te", "10ti", "10", "10", "10ti", "10tem", "1-ata", "1at", "1atům", "1ata", "1ata", "1atech", "1aty")

        //
        // $this->vzor žena
        //
    , array("ž", "-[aeiouyáéíóúý]ka", "0ky", "0ce", "0ku", "0ko", "0ce", "0kou", "-0ky", "0k", "0kám", "0ky", "0ky", "0kách", "0kami")
    , array("ž", "-ka", "ky", "ce", "ku", "ko", "ce", "kou", "-ky", "ek", "kám", "ky", "ky", "kách", "kami")
    , array("ž", "-[bdghkmnptvz]ra", "0ry", "0ře", "0ru", "0ro", "0ře", "0rou", "-0ry", "0er", "0rám", "0ry", "0ry", "0rách", "0rami")
    , array("ž", "-ra", "ry", "ře", "ru", "ro", "ře", "rou", "-ry", "r", "rám", "ry", "ry", "rách", "rami")
    , array("ž", "-[tdbnvmp]a", "0y", "0ě", "0u", "0o", "0ě", "0ou", "-0y", "0", "0ám", "0y", "0y", "0ách", "0ami")
    , array("ž", "-cha", "chy", "še", "chu", "cho", "še", "chou", "-chy", "ch", "chám", "chy", "chy", "chách", "chami")
    , array("ž", "-[gh]a", "0y", "ze", "0u", "0o", "ze", "0ou", "-0y", "0", "0ám", "0y", "0y", "0ách", "0ami")
    , array("ž", "-ňa", "ni", "ně", "ňou", "ňo", "ni", "ňou", "-ně/ničky", "ň", "ňám", "ně/ničky", "ně/ničky", "ňách", "ňami")
    , array("ž", "-[šč]a", "0i", "0e", "0u", "0o", "0e", "0ou", "-0e/0i", "0", "0ám", "0e/0i", "0e/0i", "0ách", "0ami")
    , array("ž", "-a", "y", "e", "u", "o", "e", "ou", "-y", "", "ám", "y", "y", "ách", "ami")

        // vz. píseň
    , array("ž", "-eň", "ně", "ni", "eň", "ni", "ni", "ní", "-ně", "ní", "ním", "ně", "ně", "ních", "němi")
    , array("ž", "-oň", "oně", "oni", "oň", "oni", "oni", "oní", "-oně", "oní", "oním", "oně", "oně", "oních", "oněmi")
    , array("ž", "-[ě]j", "0je", "0ji", "0j", "0ji", "0ji", "0jí", "-0je", "0jí", "0jím", "0je", "0je", "0jích", "0jemi")

        //
        // $this->vzor růže
        //
    , array("ž", "-ev", "ve", "vi", "ev", "vi", "vi", "ví", "-ve", "ví", "vím", "ve", "ve", "vích", "vemi")
    , array("ž", "-ice", "ice", "ici", "ici", "ice", "ici", "icí", "-ice", "ic", "icím", "ice", "ice", "icích", "icemi")
    , array("ž", "-e", "e", "i", "i", "e", "i", "í", "-e", "í", "ím", "e", "e", "ích", "emi")

        //
        // $this->vzor píseň
        //
    , array("ž", "-[eaá][jžň]", "10e/10i", "10i", "10", "10i", "10i", "10í", "-10e/10i", "10í", "10ím", "10e", "10e", "10ích", "10emi")
    , array("ž", "-[eayo][š]", "10e/10i", "10i", "10", "10i", "10i", "10í", "10e/10i", "10í", "10ím", "10e", "10e", "10ích", "10emi")
    , array("ž", "-[íy]ň", "0ně", "0ni", "0ň", "0ni", "0ni", "0ní", "-0ně", "0ní", "0ním", "0ně", "0ně", "0ních", "0němi")
    , array("ž", "-[íyý]ňe", "0ně", "0ni", "0ň", "0ni", "0ni", "0ní", "-0ně", "0ní", "0ním", "0ně", "0ně", "0ních", "0němi")
    , array("ž", "-[ťďž]", "0e", "0i", "0", "0i", "0i", "0í", "-0e", "0í", "0ím", "0e", "0e", "0ích", "0emi")
    , array("ž", "-toř", "toře", "toři", "toř", "toři", "toři", "toří", "-toře", "toří", "tořím", "toře", "toře", "tořích", "tořemi")
    , array("ž", "-ep", "epi", "epi", "ep", "epi", "epi", "epí", "epi", "epí", "epím", "epi", "epi", "epích", "epmi")

        //
        // $this->vzor kost
        //
    , array("ž", "-st", "sti", "sti", "st", "sti", "sti", "stí", "-sti", "stí", "stem", "sti", "sti", "stech", "stmi")
    , array("ž", "ves", "vsi", "vsi", "ves", "vsi", "vsi", "vsí", "vsi", "vsí", "vsem", "vsi", "vsi", "vsech", "vsemi")

        //
        //
        // $this->vzor Amadeus, Celsius, Kumulus, rektikulum, praktikum
        //
    , array("m", "-[e]us", "0a", "0u/0ovi", "0a", "0e", "0u/0ovi", "0em", "0ové", "0ů", "0ům", "0y", "0ové", "0ích", "0y")
    , array("m", "-[i]us", "0a", "0u/0ovi", "0a", "0e", "0u/0ovi", "0em", "0ové", "0ů", "0ům", "0usy", "0ové", "0ích", "0usy")
    , array("m", "-[i]s", "0se", "0su/0sovi", "0se", "0se/0si", "0su/0sovi", "0sem", "0sy/0sové", "0sů", "0sům", "0sy", "0sy/0ové", "0ech", "0sy")
    , array("m", "výtrus", "výtrusu", "výtrusu", "výtrus", "výtruse", "výtrusu", "výtrusem", "výtrusy", "výtrusů", "výtrusům", "výtrusy", "výtrusy", "výtrusech", "výtrusy")
    , array("m", "trus", "trusu", "trusu", "trus", "truse", "trusu", "trusem", "trusy", "trusů", "trusům", "trusy", "trusy", "trusech", "trusy")
    , array("m", "-[aeioumpts][lnmrktp]us", "10u/10a", "10u/10ovi", "10us/10a", "10e", "10u/10ovi", "10em", "10y/10ové", "10ů", "10ům", "10y", "10y/10ové", "10ech", "10y")
    , array("s", "-[l]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0", "0ům", "0a", "0a", "0ech", "0y")
    , array("s", "-[k]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0", "0ům", "0a", "0a", "0cích", "0y")
    , array("s", "-[i]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0í", "0ům", "0a", "0a", "0iích", "0y")
    , array("s", "-[i]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0ejí", "0ům", "0a", "0a", "0ejích", "0y")
    , array("s", "-io", "0a", "0u", "0", "0", "0u", "0em", "0a", "0í", "0ům", "0a", "0a", "0iích", "0y")

        //
        // $this->vzor sedlák
        //

    , array("m", "-[aeiouyáéíóúý]r", "0ru/0ra", "0ru/0rovi", "0r/0ra", "0re", "0ru/0rovi", "0rem", "-0ry/-0rové", "0rů", "0rům", "0ry", "0ry/0rové", "0rech", "0ry")
        // , array( "m","-[aeiouyáéíóúý]r","0ru/0ra","0ru/0rovi","0r/0ra","0re","0ru/0rovi","0rem",     "-0ry/-0ři","0rů","0rům","0ry","0ry/0ři", "0rech","0ry" )
    , array("m", "-r", "ru/ra", "ru/rovi", "r/ra", "ře", "ru/rovi", "rem", "-ry/-rové", "rů", "rům", "ry", "ry/rové", "rech", "ry")
        // , array( "m","-r",              "ru/ra",  "ru/rovi",  "r/ra",  "ře", "ru/rovi",   "rem",     "-ry/-ři", "rů","rům","ry",    "ry/ři",  "rech", "ry" )
    , array("m", "-[mnp]en", "0enu/0ena", "0enu/0enovi", "0en/0na", "0ene", "0enu/0enovi", "0enem", "-0eny/0enové", "0enů", "0enům", "0eny", "0eny/0enové", "0enech", "0eny")
    , array("m", "-[bcčdstvz]en", "0nu/0na", "0nu/0novi", "0en/0na", "0ne", "0nu/0novi", "0nem", "-0ny/0nové", "0nů", "0nům", "0ny", "0ny/0nové", "0nech", "0ny")
    , array("m", "-[dglmnpbtvzs]", "0u/0a", "0u/0ovi", "0/0a", "0e", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "0ech", "0y")
    , array("m", "-[x]", "0u/0e", "0u/0ovi", "0/0e", "0i", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "0ech", "0y")
    , array("m", "sek", "seku/seka", "seku/sekovi", "sek/seka", "seku", "seku/sekovi", "sekem", "seky/sekové", "seků", "sekům", "seky", "seky/sekové", "secích", "seky")
    , array("m", "výsek", "výseku/výseka", "výseku/výsekovi", "výsek/výseka", "výseku", "výseku/výsekovi", "výsekem", "výseky/výsekové", "výseků", "výsekům", "výseky", "výseky/výsekové", "výsecích", "výseky")
    , array("m", "zásek", "záseku/záseka", "záseku/zásekovi", "zásek/záseka", "záseku", "záseku/zásekovi", "zásekem", "záseky/zásekové", "záseků", "zásekům", "záseky", "záseky/zásekové", "zásecích", "záseky")
    , array("m", "průsek", "průseku/průseka", "průseku/průsekovi", "průsek/průseka", "průseku", "průseku/průsekovi", "průsekem", "průseky/průsekové", "průseků", "výsekům", "průseky", "průseky/průsekové", "průsecích", "průseky")
    , array("m", "-[cčšždnňmpbrstvz]ek", "0ku/0ka", "0ku/0kovi", "0ek/0ka", "0ku", "0ku/0kovi", "0kem", "-0ky/0kové", "0ků", "0kům", "0ky", "0ky/0kové", "0cích", "0ky")
    , array("m", "-[k]", "0u/0a", "0u/0ovi", "0/0a", "0u", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "cích", "0y")
    , array("m", "-ch", "chu/cha", "chu/chovi", "ch/cha", "chu/cha", "chu/chovi", "chem", "-chy/chové", "chů", "chům", "chy", "chy/chové", "ších", "chy")
    , array("m", "-[h]", "0u/0a", "0u/0ovi", "0/0a", "0u", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "zích", "0y")
    , array("m", "-e[mnz]", "0u/0a", "0u/0ovi", "e0/e0a", "0e", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "0ech", "0y")

        //
        // $this->vzor muž
        //
    , array("m", "-ec", "ce", "ci/covi", "ec/ce", "če", "ci/covi", "cem", "-ce/cové", "ců", "cům", "ce", "ce/cové", "cích", "ci")
    , array("m", "-[cčďšňřťž]", "0e", "0i/0ovi", "0e", "0i", "0i/0ovi", "0em", "-0e/0ové", "0ů", "0ům", "0e", "0e/0ové", "0ích", "0i")
    , array("m", "-oj", "oje", "oji/ojovi", "oj/oje", "oji", "oji/ojovi", "ojem", "-oje/ojové", "ojů", "ojům", "oje", "oje/ojové", "ojích", "oji")

        // $this->vzory pro přetypování rodu
    , array("m", "-[gh]a", "0y", "0ovi", "0u", "0o", "0ovi", "0ou", "0ové", "0ů", "0ům", "0y", "0ové", "zích", "0y")
    , array("m", "-[k]a", "0y", "0ovi", "0u", "0o", "0ovi", "0ou", "0ové", "0ů", "0ům", "0y", "0ové", "cích", "0y")
    , array("m", "-a", "y", "ovi", "u", "o", "ovi", "ou", "ové", "ů", "ům", "y", "ové", "ech", "y")

    , array("ž", "-l", "le", "li", "l", "li", "li", "lí", "le", "lí", "lím", "le", "le", "lích", "lemi")
    , array("ž", "-í", "í", "í", "í", "í", "í", "í", "í", "ích", "ím", "í", "í", "ích", "ími")
    , array("ž", "-[jř]", "0e", "0i", "0", "0i", "0i", "0í", "0e", "0í", "0ím", "0e", "0e", "0ích", "0emi")
    , array("ž", "-[č]", "0i", "0i", "0", "0i", "0i", "0í", "0i", "0í", "0ím", "0i", "0i", "0ích", "0mi")
    , array("ž", "-[š]", "0i", "0i", "0", "0i", "0i", "0í", "0i", "0í", "0ím", "0i", "0i", "0ích", "0emi")

    , array("s", "-[sljřň]e", "0ete", "0eti", "0e", "0e", "0eti", "0etem", "0ata", "0at", "0atům", "0ata", "0ata", "0atech", "0aty")
        // , array( "ž","-cí",        "cí", "cí",  "cí", "cí", "cí", "cí",   "cí", "cích", "cím", "cí", "cí", "cích", "cími" )
        // čaj, prodej, ondřej, žokej
    , array("m", "-j", "je", "ji", "j", "ji", "ji", "jem", "je/jové", "jů", "jům", "je", "je/jové", "jích", "ji")
        // josef, detlef, ... ?
    , array("m", "-f", "fa", "fu/fovi", "f/fa", "fe", "fu/fovi", "fem", "fy/fové", "fů", "fům", "fy", "fy/fové", "fech", "fy")
        // zbroj, výzbroj, výstroj, trofej, neteř
        // jiří, podkoní, ... ?
    , array("m", "-í", "ího", "ímu", "ího", "í", "ímu", "ím", "í", "ích", "ím", "í", "í", "ích", "ími")
        // hugo
    , array("m", "-go", "a", "govi", "ga", "ga", "govi", "gem", "gové", "gů", "gům", "gy", "gové", "zích", "gy")
        // kvido
    , array("m", "-o", "a", "ovi", "a", "a", "ovi", "em", "ové", "ů", "ům", "y", "ové", "ech", "y")


        // doplňky
        // některá pomnožná jména
    , array(null, "-[tp]y", null, null, null, null, null, null, "-0y", "0", "0ům", "0y", "0y", "0ech", "0ami")
    , array(null, "-[k]y", null, null, null, null, null, null, "-0y", "e0", "0ám", "0y", "0y", "0ách", "0ami")

        // změny rodu
    , array("ž", "-ar", "ary", "aře", "ar", "ar", "ar", "ar", "ary", "ar", "arám", "ary", "ary", "arách", "arami")
    , array("ž", "-am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am")
    , array("ž", "-er", "er", "er", "er", "er", "er", "er", "ery", "er", "erám", "ery", "ery", "erách", "erami")

    , array("m", "-oe", "oema", "oemovi", "oema", "oeme", "emovi", "emem", "oemové", "oemů", "oemům", "oemy", "oemové", "oemech", "oemy")

    );

    //  Výjimky:
    //  $this->v1 - přehlásky
    // :  důl ... dol, stůl ... stol, nůž ... nož, hůl ... hole, půl ... půle
    //                      1.p   náhrada   4.p.
    protected $v1 = array(
        array("osel", "osl", "osla")
    , array("karel", "karl", "karla")
    , array("karel", "karl", "karla")
    , array("pavel", "pavl", "pavla")
    , array("pavel", "pavl", "pavla")
    , array("havel", "havl", "havla")
    , array("havel", "havl", "havla")
    , array("bořek", "bořk", "bořka")
    , array("bořek", "bořk", "bořka")
    , array("luděk", "luďk", "luďka")
    , array("luděk", "luďk", "luďka")
    , array("pes", "ps", "psa")
    , array("pytel", "pytl", "pytel")
    , array("ocet", "oct", "octa")
    , array("chléb", "chleb", "chleba")
    , array("chleba", "chleb", "chleba")
    , array("pavel", "pavl", "pavla")
    , array("kel", "kl", "kel")
    , array("sopel", "sopl", "sopel")
    , array("kotel", "kotl", "kotel")
    , array("posel", "posl", "posla")
    , array("důl", "dol", "důl")
    , array("sůl", "sole", "sůl")
    , array("vůl", "vol", "vola")
    , array("půl", "půle", "půli")
    , array("hůl", "hole", "hůl")
    , array("stůl", "stol", "stůl")
    , array("líh", "lih", "líh")
    , array("sníh", "sněh", "sníh")
    , array("zář", "záře", "zář")
    , array("svatozář", "svatozáře", "svatozář")
    , array("kůň", "koň", "koně")
    , array("tůň", "tůňe", "tůň")
        // --- !
    , array("prsten", "prstýnek", "prstýnku")
    , array("smrt", "smrť", "smrt")
    , array("vítr", "větr", "vítr")
    , array("stupeň", "stupň", "stupeň")
    , array("peň", "pň", "peň")
    , array("cyklus", "cykl", "cyklus")
    , array("dvůr", "dvor", "dvůr")
    , array("zeď", "zď", "zeď")
    , array("účet", "účt", "účet")
    , array("mráz", "mraz", "mráz")
    , array("hnůj", "hnoj", "hnůj")
    , array("skrýš", "skrýše", "skrýš")
    , array("nehet", "neht", "nehet")
    , array("veš", "vš", "veš")
    , array("déšť", "dešť", "déšť")
    , array("myš", "myše", "myš")
    );

    protected $aCmpReg = array();


    public function __construct()
    {

        $this->aCmpReg = array_fill(0, 9, "");

        // $this->v10 - zmena rodu na muzsky
        $this->v10 = array(
            "sleď",
            "saša",
            "saša",
            "dešť",
            "koň",
            "chlast",
            "plast",
            "termoplast",
            "vězeň",
            "sťežeň",
            "papež",
            "ďeda",
            "zeť",
            "háj",
            "lanýž",
            "sluha",
            "muž",
            "velmož",
            "maťej",
            "maťej",
            "táta",
            "kolega",
            "mluvka",
            "strejda",
            "polda",
            "moula",
            "šmoula",
            "slouha",
            "drákula",
            "test",
            "rest",
            "trest",
            "arest",
            "azbest",
            "ametyst",
            "chřest",
            "protest",
            "kontest",
            "motorest",
            "most",
            "host",
            "kříž",
            "stupeň",
            "peň",
            "čaj",
            "prodej",
            "výdej",
            "výprodej",
            "ďej",
            "zloďej",
            "žokej",
            "hranostaj",
            "dobroďej",
            "darmoďej",
            "čaroďej",
            "koloďej",
            "sprej",
            "displej",
            "aleš",
            "aleš",
            "ambrož",
            "ambrož",
            "tomáš",
            "lukáš",
            "tobiáš",
            "jiří",
            "tomáš",
            "lukáš",
            "tobiáš",
            "jiří",
            "podkoní",
            "komoří",
            "jirka",
            "jirka",
            "ilja",
            "ilja",
            "pepa",
            "ondřej",
            "ondřej",
            "andrej",
            "andrej",
    //  "josef",
            "mikuláš",
            "mikuláš",
            "mikoláš",
            "mikoláš",
            "kvido",
            "kvido",
            "hugo",
            "hugo",
            "oto",
            "oto",
            "otto",
            "otto",
            "alexej",
            "alexej",
            "ivo",
            "ivo",
            "bruno",
            "bruno",
            "alois",
            "alois",
            "bartoloměj",
            "bartoloměj",
            "noe",
            "noe");

        // $this->v11 - zmena rodu na zensky
        $this->v11 = array(
            "vš",
            "dešť",
            "zteč",
            "řeč",
            "křeč",
            "kleč",
            "maštal",
            "vš",
            "kancelář",
            "závěj",
            "zvěř",
            "sbeř",
            "neteř",
            "ves",
            "rozkoš",
            // "myša",
            "postel",
            "prdel",
            "koudel",
            "koupel",
            "ocel",
            "digestoř",
            "konzervatoř",
            "oratoř",
            "zbroj",
            "výzbroj",
            "výstroj",
            "trofej",
            "obec",
            "otep",
            "miriam",
            // "miriam",
            "ester",
            "dagmar"
        );

            // "transmise,
        // $this->v12 - zmena rodu na stredni
        $this->v12 = array(
            "nemluvňe",
            "slůně",
            "kůzle",
            "sele",
            "osle",
            "zvíře",
            "kuře",
            "tele",
            "prase",
            "house",
            "vejce",
        );


        // $this->v0 - nedořešené výjimky
        $this->v0 = array(
            "sten",
//      "ester,
//      "dagmar,
//      "ovoce,
//      "zeus,
//      "zbroj,
//      "výzbroj,
//      "výstroj,
//      "obec,
//      "konzervatoř,
//      "digestoř,
//      "humus,
//      "muka,
//      "noe,
//      "noe,
        );
        //  "miriam,
        //  "miriam,
        // Je Nikola ženské nebo mužské jméno??? (podobně Sáva)
        // $this->v3 - různé odchylky ve skloňování
        //    - časem by bylo vhodné opravit
        $this->v3 = array(
            "jméno",
            "myš",
            "vězeň",
            "sťežeň",
            "oko",
            "sole",
            "šach",
            "veš",
            "myš",
            "klášter",
            "kněz",
            "král",
            "zď",
            "sto",
            "smrt",
            "leden",
            "len",
            "les",
            "únor",
            "březen",
            "duben",
            "květen",
            "červen",
            "srpen",
            "říjen",
            "pantofel",
            "žába",
            "zoja",
            "zoja",
            "zoe",
            "zoe",
        );

        $this->astrTvar = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
    }

    /**
     * @author Jan Navratil <jan.navratil@heureka.cz>
     * @param bool $debugMode
     */
    public function setDebugMode(bool $debugMode)
    {
        $this->isDebugMode = $debugMode;
    }

//
//  Fce isShoda vraci index pri shode koncovky (napr. isShoda("-lo","kolo"), isShoda("ko-lo","motovidlo"))
//  nebo pri rovnosti slov (napr. isShoda("molo","molo").
//  Jinak je navratova hodnota -1.
//
    private function isShoda($vz, $txt)
    {
        $txt = mb_strtolower($txt, 'UTF-8');
        $i = mb_strlen($vz, 'UTF-8');
        $j = mb_strlen($txt, 'UTF-8');

        if ($i == 0 || $j == 0)
            return -1;
        $i--;
        $j--;

        $nCmpReg = 0;

	    $txtChar = preg_split('//u', $txt, -1, PREG_SPLIT_NO_EMPTY);
	    $vzChar = preg_split('//u', $vz, -1, PREG_SPLIT_NO_EMPTY);

        while ($i >= 0 && $j >= 0) {
            if ($vzChar[$i] == "]") {
                $i--;
                $quit = 1;
                while ($i >= 0 && $vzChar[$i] != "[") {
                    if ($vzChar[$i] == $txtChar[$j]) {
                        $quit = 0;
                        $this->aCmpReg[$nCmpReg] = $vzChar[$i];
                        $nCmpReg++;
                    }
                    $i--;
                }

                if ($quit == 1)
                    return -1;
            } else {
                if ($vzChar[$i] == '-')
                    return $j + 1;
                if ($vzChar[$i] != $txtChar[$j])
                    return -1;
            }
            $i--;
            $j--;
        }
        if ($i < 0 && $j < 0)
            return 0;
        if ($i >= 0 && $vzChar[$i] == '-')
            return 0;

        return -1;
    }

//
// Transformace: ďi,ťi,ňi,ďe,ťe,ňe ... di,ti,ni,dě,tě,ně
//               + "ch" -> "#"
//
    private function Xdetene($txt2)
    {
        $XdeteneRV = "";
        $length = mb_strlen($txt2, 'UTF-8');
        for ($XdeteneI = 0; $XdeteneI < $length - 1; $XdeteneI++) {
            $charN = mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
            $charNplus1 = mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8');
            if ($charN == "ď" && ($charNplus1 == "e" || $charNplus1 == "i" || $charNplus1 == "í")) {
                $XdeteneRV .= "d";
                if ($charNplus1 == "e") {
                    $XdeteneRV .= "ě";
                    $XdeteneI++;
                }
            } else if ($charN == "ť" && ($charNplus1 == "e" || $charNplus1 == "i" || $charNplus1 == "í")) {
                $XdeteneRV .= "t";
                if ($charNplus1 == "e") {
                    $XdeteneRV .= "ě";
                    $XdeteneI++;
                }
            } else if ($charN == "ň" && ($charNplus1 == "e" || $charNplus1 == "i" || $charNplus1 == "í")) {
                $XdeteneRV .= "n";
                if ($charNplus1 == "e") {
                    $XdeteneRV .= "ě";
                    $XdeteneI++;
                }
            } else
                $XdeteneRV .= $charN;
        }

        if ($XdeteneI == $length - 1)
            $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');

        return $XdeteneRV;
    }

//
// Transformace: di,ti,ni,dě,tě,ně ... ďi,ťi,ňi,ďe,ťe,ňe
//
    private function Xedeten($txt2)
    {
        $XdeteneRV = "";
        $length = mb_strlen($txt2, 'UTF-8');
        for ($XdeteneI = 0; $XdeteneI < $length - 1; $XdeteneI++) {

            $charN = mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
            $charNplus1 = mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8');

            if ($charN == "d" && ($charNplus1 == "ě" || $charNplus1 == "i")) {
                $XdeteneRV .= "ď";
                if ($charNplus1 == "ě") {
                    $XdeteneRV .= "e";
                    $XdeteneI++;
                }
            } else if ($charN == "t" && ($charNplus1 == "ě" || $charNplus1 == "i")) {
                $XdeteneRV .= "ť";
                if ($charNplus1 == "ě") {
                    $XdeteneRV .= "e";
                    $XdeteneI++;
                }
            } else if ($charN == "n" && ($charNplus1 == "ě" || $charNplus1 == "i")) {
                $XdeteneRV .= "ň";
                if ($charNplus1 == "ě") {
                    $XdeteneRV .= "e";
                    $XdeteneI++;
                }
            } else
                $XdeteneRV .= $charN;
        }

        if ($XdeteneI == $length - 1)
            $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');

        return $XdeteneRV;
    }

//
// Funkce pro sklonovani
//

    private function CmpFrm($txt)
    {
        $CmpFrmRV = "";
        $length = mb_strlen($txt, 'UTF-8');
	    $txtChar = preg_split('//u', $txt, -1, PREG_SPLIT_NO_EMPTY);
        for ($CmpFrmI = 0; $CmpFrmI < $length; $CmpFrmI++) {
            $char = $txtChar[$CmpFrmI];
            if ($char == "0")
                $CmpFrmRV .= $this->aCmpReg[0];
            else if ($char == "1")
                $CmpFrmRV .= $this->aCmpReg[1];
            else if ($char == "2")
                $CmpFrmRV .= $this->aCmpReg[2];
            else
                $CmpFrmRV .= $char;

        }
        return $CmpFrmRV;
    }

// Funkce pro sklonovani slova do daneho podle
// daneho $this->vzoru
    private function Sklon($nPad, $vzndx, $txt, $zivotne = false)
    {
        $cnt = count($this->vzor);
        if ($vzndx >= $cnt || $vzndx < 0)
            return "???";

        $txt3 = $this->Xedeten($txt);
        $kndx = $this->isShoda($this->vzor[$vzndx][1], $txt3);
        if ($kndx < 0 || $nPad < 1 || $nPad > 14) //8-14 je pro plural
            return "???";

        if ($this->vzor[$vzndx][$nPad] == null)
            return null;

        if (!$this->isDebugMode & $nPad == 1) // 1. pad nemenime
            $rv = $this->Xdetene($txt3);
        else
            $rv = $this->LeftStr($kndx, $txt3) . '-' . $this->CmpFrm($this->vzor[$vzndx][$nPad]);

        if ($this->isDebugMode) //preskoceni filtrovani
            return $rv;

	    $rvChar = preg_split('//u', $rv, -1, PREG_SPLIT_NO_EMPTY);

        // Formatovani zivotneho sklonovani
        // - nalezeni pomlcky
        $length = mb_strlen($rv, 'UTF-8');
        for ($nnn = 0; $nnn < $length; $nnn++)
            if ($rvChar[$nnn] == "-")
                break;

        $ndx1 = $nnn;

        // - nalezeni lomitka
        for ($nnn = 0; $nnn < $length; $nnn++)
            if ($rvChar[$nnn] == "/")
                break;

        $ndx2 = $nnn;


        if ($ndx1 != $length && $ndx2 != $length) {
            if ($zivotne) {
                // "text-xxx/yyy" -> "textyyy"
                $rv = $this->LeftStr($ndx1, $rv) . $this->RightStr($ndx2 + 1, $rv, $length);
            } else {
                // "text-xxx/yyy" -> "text-xxx"
                $rv = $this->LeftStr($ndx2, $rv);
            }
            $length = mb_strlen($rv, 'UTF-8');
            $rvChar = preg_split('//u', $rv, -1, PREG_SPLIT_NO_EMPTY);
        }


        // vypusteni pomocnych znaku
        $txt3 = "";
        for ($nnn = 0; $nnn < $length; $nnn++) {
            $char = $rvChar[$nnn];
            if (!($char == '-' || $char == '/'))
                $txt3 .= $char;
        }
        $rv = $this->Xdetene($txt3);

        return $rv;
//  return $this->LeftStr( $kndx, $txt ) + $this->vzor[$vzndx][$nPad];
    }

//
// Funkce pro praci s retezci
//
// - levy retezec do indexu n (bez tohoto indexu)
    private function LeftStr($n, $txt)
    {
        return mb_substr($txt, 0, $n, 'UTF-8');
    }

// - pravy retezec od indexu n (vcetne)
    private function RightStr($n, $txt, $length)
    {
        return mb_substr($txt, $n, $length, 'UTF-8');
    }

    /**
     *
     * @param $text
     * @param bool $zivotne
     * @param string $preferovanyRod
     * @return array
     */
    public function inflect($text, $zivotne = false, $preferovanyRod = '')
    {
        $aTxt = explode(' ', $text);

        $this->PrefRod = "0";
        $out = array();
        $cnt = count($aTxt);
        $astrTvarFirst = mb_substr($this->astrTvar[0], 0, 1, 'UTF-8');
        $prefRodFirst = mb_substr($this->PrefRod, 0, 1, 'UTF-8');
        for ($i = $cnt - 1; $i >= 0; $i--) {
            // vysklonovani
            $this->skl2($aTxt[$i], $preferovanyRod, $zivotne);

            // vynuceni rodu podle posledniho slova
            if ($i == $cnt - 1)
                $this->PrefRod = $this->astrTvar[0];

            // pokud nenajdeme $this->vzor tak nesklonujeme
            if (null === $this->astrTvar[0] && $i < $cnt - 1 && $prefRodFirst != '?') {
                for ($j = 1; $j < 15; $j++)
                    $this->astrTvar[$j] = $aTxt[$i];
            }

            if ($astrTvarFirst == '?')
                $this->astrTvar[0] = '';

            if ($i < $cnt) {
                for ($j = 1; $j < 15; $j++) {
                    if (null === $this->astrTvar[$j] && !isset($out[$j])) {
                        $out[$j] = $this->astrTvar[$j];
                    } else {
                        $out[$j] = $this->astrTvar[$j] . (isset($out[$j]) ? ' ' . $out[$j] : '');
                    }
                }
            } else {
                for ($j = 1; $j < 15; $j++)
                    $out[$j] = $this->astrTvar[$j];
            }
        }
        return $out;
    }

// Sklonovani podle standardniho seznamu pripon
    private function SklStd($slovo, $ii, $zivotne)
    {
        $cnt = count($this->vzor);
        if ($ii < 0 || $ii > $cnt)
            $this->astrTvar[0] = "!!!???";

        // - seznam nedoresenych slov
        $cnt = count($this->v0);
        for ($jj = 0; $jj < $cnt; $jj++)
            if ($this->isShoda($this->v0[$jj], $slovo) >= 0) {
                //str = "Seznam výjimek [" + $jj + "]. "
                //alert(str + "Lituji, toto $slovo zatím neumím správně vyskloňovat.");
                return null;
            }

        // nastaveni rodu
        $this->astrTvar[0] = $this->vzor[$ii][0];

        // vlastni sklonovani
        for ($jj = 1; $jj < 15; $jj++)
            $this->astrTvar[$jj] = $this->Sklon($jj, $ii, $slovo, $zivotne);

        // - seznam nepresneho sklonovani
        for ($jj = 0; $jj < count($this->v3); $jj++)
            if ($this->isShoda($this->v3[$jj], $slovo) >= 0) {
                //alert("Pozor, v některých pádech nemusí být skloňování tohoto slova přesné.");
                return;
            }

//  return SklFmt( $this->astrTvar );
    }

// Pokud je index>=0, je $slovo výjimka ze seznamu "$vx"(v10,...), definovaného výše.
    private function NdxInVx($vx, $slovo)
    {
        $cnt = count($vx);
        for ($vxi = 0; $vxi < $cnt; $vxi++)
            if ($slovo == $vx[$vxi])
                return $vxi;

        return -1;
    }

// Pokud je index>=0, je $slovo výjimka ze seznamu "$vx", definovaného výše.
    private function ndxV1($slovo)
    {
        $cnt = count($this->v1);
        for ($this->v1i = 0; $this->v1i < $cnt; $this->v1i++)
            if ($slovo == $this->v1[$this->v1i][0])
                return $this->v1i;

        return -1;
    }

    private function StdNdx($slovo)
    {
        $cnt = count($this->vzor);
        $char = mb_substr($this->PrefRod, 0, 1, 'UTF-8');
        for ($iii = 0; $iii < $cnt; $iii++) {
            // filtrace rodu
            if ($char != "0" && $char != mb_substr($this->vzor[$iii][0], 0, 1, 'UTF-8'))
                continue;

            if ($this->isShoda($this->vzor[$iii][1], $slovo) >= 0)
                break;
        }

        if ($iii >= $cnt)
            return -1;

        return $iii;
    }

// Sklonovani podle seznamu vyjimek typu $this->v1
    private function SklV1($slovo, $ii, $zivotne)
    {
        $this->SklStd($this->v1[$ii][1], $this->StdNdx($this->v1[$ii][1]), $zivotne);
        $this->astrTvar[1] = $slovo; //1.p nechame jak je
        $this->astrTvar[4] = $this->v1[$ii][2];
    }

	/**
	 * Nastavuje globalni astrTVar (sklonovane tvary) a PrefRod (vynuceni rodu predchozich slov)
	 */
    private function skl2($slovo, $preferovanyRod = '', $zivotne = false)
    {
        $this->astrTvar[0] = "???";
        for ($ii = 1; $ii < 15; $ii++)
            $this->astrTvar[$ii] = "";

        $flgV1 = $this->ndxV1($slovo);
        if ($flgV1 >= 0) {
            $slovoV1 = $slovo;
            $slovo = $this->v1[$flgV1][1];
        }
//  if( $ii>=0 )
//  {
//    $this->astrTvar[1] = "v1: " + $ii;
//    $this->SklV1( $slovo, $ii );
//    return SklFmt( $this->astrTvar );
//    return 0;
//  }

        $slovo = $this->Xedeten($slovo);

        //$vNdx = 0;

        // Pretypovani rodu?
        $vs = $preferovanyRod;
        if ($vs == "z")
            $vs = "ž";
        if ($vs == "m" || $vs == "ž" || $vs == "s")
            $this->PrefRod = $vs;
        else
            $vs = "";


        if ($this->NdxInVx($this->v10, $slovo) >= 0)
            $this->PrefRod = "m";
        else if ($this->NdxInVx($this->v11, $slovo) >= 0)
            $this->PrefRod = "ž";
        else if ($this->NdxInVx($this->v12, $slovo) >= 0)
            $this->PrefRod = "s";

        // Nalezeni $this->vzoru
        $ii = $this->StdNdx($slovo);
        if ($ii < 0) {
            //alert("Chyba: proto toto $slovo nebyl nalezen $this->vzor.");
            return -1; //    return "\n  Sorry, nenasel jsem $this->vzor.";
        }

        // Vlastni sklonovani
        $this->SklStd($slovo, $ii, $zivotne);

        if ($flgV1 >= 0) {
            $this->astrTvar[1] = $slovoV1; //1.p nechame jak je
            $this->astrTvar[4] = $this->v1[$flgV1][2];
        }

        // Pokud bylo zadané slovo s velkým písmenem na začátku,
        // vrať velké písmeno i ve skloňovaných tvarech
        $firstChar = mb_substr($slovo, 0, 1, 'UTF-8');
        if (mb_strtoupper($firstChar) === $firstChar)
        {
            for ($i = 1; $i <= 15; $i++)
            {
                if ($this->astrTvar[$i])
                {
                    $this->astrTvar[$i] = mb_convert_case($this->astrTvar[$i], MB_CASE_TITLE, "UTF-8");
                }
            }
        }

        return 0; //return SklFmt( $this->astrTvar ); //  return "$this->vzor: "+$this->vzor[$ii][1];
    }

    /**
     * Try to detect feminine genus by given surname
     * - only basic detection
     * @author Jan Navratil <jan.navratil@heureka.cz>
     * @param $surname
     * @return null|string
     */
    public function isFeminineGenusSurname($surname)
    {
        if ('ova' == str_replace('á', 'a', mb_substr(mb_strtolower($surname, 'UTF-8'), -3, 3, 'UTF-8'))) {
            return self::GENUS_FEMININE;
        } else {
            return null;
        }
    }

}
