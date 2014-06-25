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
 * $x = new Sklonovani();
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

    public function __construct()
    {
        //
        //  Databaze vzoru pro sklonovani
        //
        $this->vzor = Array();
        $nvz = 0;
        $this->isDbgMode = false;
        //
        // Přídavná jména a zájmena
        //
        $this->vzor[$nvz++] = Array("m", "-ký", "kého", "kému", "ký/kého", "ký", "kém", "kým", "-ké/-cí", "kých", "kým", "ké", "-ké/-cí", "kých", "kými");
        $this->vzor[$nvz++] = Array("m", "-rý", "rého", "rému", "rý/rého", "rý", "rém", "rým", "-ré/-ří", "rých", "rým", "ré", "-ré/-ří", "rých", "rými");
        $this->vzor[$nvz++] = Array("m", "-chý", "chého", "chému", "chý/chého", "chý", "chém", "chým", "-ché/-ší", "chých", "chým", "ché", "-ché/-ší", "chých", "chými");
        $this->vzor[$nvz++] = Array("m", "-hý", "hého", "hému", "hý/hého", "hý", "hém", "hým", "-hé/-zí", "hých", "hým", "hé", "-hé/-zí", "hých", "hými");
        $this->vzor[$nvz++] = Array("m", "-ý", "ého", "ému", "ý/ého", "ý", "ém", "ým", "-é/-í", "ých", "ým", "é", "-é/-í", "ých", "ými");
        $this->vzor[$nvz++] = Array("m", "-[aeěií]cí", "0cího", "0címu", "0cí/0cího", "0cí", "0cím", "0cím", "0cí", "0cích", "0cím", "0cí", "0cí", "0cích", "0cími");
        $this->vzor[$nvz++] = Array("ž", "-[aeěií]cí", "0cí", "0cí", "0cí", "0cí", "0cí", "0cí", "0cí", "0cích", "0cím", "0cí", "0cí", "0cích", "0cími");
        $this->vzor[$nvz++] = Array("s", "-[aeěií]cí", "0cího", "0címu", "0cí/0cího", "0cí", "0cím", "0cím", "0cí", "0cích", "0cím", "0cí", "0cí", "0cích", "0cími");
        $this->vzor[$nvz++] = Array("m", "-[bcčdhklmnprsštvzž]ní", "0ního", "0nímu", "0ní/0ního", "0ní", "0ním", "0ním", "0ní", "0ních", "0ním", "0ní", "0ní", "0ních", "0ními");
        $this->vzor[$nvz++] = Array("ž", "-[bcčdhklmnprsštvzž]ní", "0ní", "0ní", "0ní", "0ní", "0ní", "0ní", "0ní", "0ních", "0ním", "0ní", "0ní", "0ních", "0ními");
        $this->vzor[$nvz++] = Array("s", "-[bcčdhklmnprsštvzž]ní", "0ního", "0nímu", "0ní/0ního", "0ní", "0ním", "0ním", "0ní", "0ních", "0ním", "0ní", "0ní", "0ních", "0ními");

        $this->vzor[$nvz++] = Array("m", "-[i]tel", "0tele", "0teli", "0tele", "0tel", "0teli", "0telem", "0telé", "0telů", "0telům", "0tele", "0telé", "0telích", "0teli");
        $this->vzor[$nvz++] = Array("m", "-[í]tel", "0tele", "0teli", "0tele", "0tel", "0teli", "0telem", "átelé", "áteli", "átelům", "átele", "átelé", "átelích", "áteli");


        $this->vzor[$nvz++] = Array("s", "-é", "ého", "ému", "é", "é", "ém", "ým", "-á", "ých", "ým", "á", "á", "ých", "ými");
        $this->vzor[$nvz++] = Array("ž", "-á", "é", "é", "ou", "á", "é", "ou", "-é", "ých", "ým", "é", "é", "ých", "ými");
        $this->vzor[$nvz++] = Array("-", "já", "mne", "mně", "mne/mě", "já", "mně", "mnou", "my", "nás", "nám", "nás", "my", "nás", "námi");
        $this->vzor[$nvz++] = Array("-", "ty", "tebe", "tobě", "tě/tebe", "ty", "tobě", "tebou", "vy", "vás", "vám", "vás", "vy", "vás", "vámi");
        $this->vzor[$nvz++] = Array("-", "my", "", "", "", "", "", "", "my", "nás", "nám", "nás", "my", "nás", "námi");
        $this->vzor[$nvz++] = Array("-", "vy", "", "", "", "", "", "", "vy", "vás", "vám", "vás", "vy", "vás", "vámi");
        $this->vzor[$nvz++] = Array("m", "on", "něho", "mu/jemu/němu", "ho/jej", "on", "něm", "ním", "oni", "nich", "nim", "je", "oni", "nich", "jimi/nimi");
        $this->vzor[$nvz++] = Array("m", "oni", "", "", "", "", "", "", "oni", "nich", "nim", "je", "oni", "nich", "jimi/nimi");
        $this->vzor[$nvz++] = Array("ž", "ony", "", "", "", "", "", "", "ony", "nich", "nim", "je", "ony", "nich", "jimi/nimi");
        $this->vzor[$nvz++] = Array("s", "ono", "něho", "mu/jemu/němu", "ho/jej", "ono", "něm", "ním", "ona", "nich", "nim", "je", "ony", "nich", "jimi/nimi");
        $this->vzor[$nvz++] = Array("ž", "ona", "ní", "ní", "ji", "ona", "ní", "ní", "ony", "nich", "nim", "je", "ony", "nich", "jimi/nimi");
        $this->vzor[$nvz++] = Array("m", "ten", "toho", "tomu", "toho", "ten", "tom", "tím", "ti", "těch", "těm", "ty", "ti", "těch", "těmi");
        $this->vzor[$nvz++] = Array("ž", "ta", "té", "té", "tu", "ta", "té", "tou", "ty", "těch", "těm", "ty", "ty", "těch", "těmi");
        $this->vzor[$nvz++] = Array("s", "to", "toho", "tomu", "toho", "to", "tom", "tím", "ta", "těch", "těm", "ta", "ta", "těch", "těmi");

        // přivlastňovací zájmena
        $this->vzor[$nvz++] = Array("m", "můj", "mého", "mému", "mého", "můj", "mém", "mým", "mí", "mých", "mým", "mé", "mí", "mých", "mými");
        $this->vzor[$nvz++] = Array("ž", "má", "mé", "mé", "mou", "má", "mé", "mou", "mé", "mých", "mým", "mé", "mé", "mých", "mými");
        $this->vzor[$nvz++] = Array("ž", "moje", "mé", "mé", "mou", "má", "mé", "mou", "moje", "mých", "mým", "mé", "mé", "mých", "mými");
        $this->vzor[$nvz++] = Array("s", "mé", "mého", "mému", "mé", "moje", "mém", "mým", "mé", "mých", "mým", "má", "má", "mých", "mými");
        $this->vzor[$nvz++] = Array("s", "moje", "mého", "mému", "moje", "moje", "mém", "mým", "moje", "mých", "mým", "má", "má", "mých", "mými");

        $this->vzor[$nvz++] = Array("m", "tvůj", "tvého", "tvému", "tvého", "tvůj", "tvém", "tvým", "tví", "tvých", "tvým", "tvé", "tví", "tvých", "tvými");
        $this->vzor[$nvz++] = Array("ž", "tvá", "tvé", "tvé", "tvou", "tvá", "tvé", "tvou", "tvé", "tvých", "tvým", "tvé", "tvé", "tvých", "tvými");
        $this->vzor[$nvz++] = Array("ž", "tvoje", "tvé", "tvé", "tvou", "tvá", "tvé", "tvou", "tvé", "tvých", "tvým", "tvé", "tvé", "tvých", "tvými");
        $this->vzor[$nvz++] = Array("s", "tvé", "tvého", "tvému", "tvého", "tvůj", "tvém", "tvým", "tvá", "tvých", "tvým", "tvé", "tvá", "tvých", "tvými");
        $this->vzor[$nvz++] = Array("s", "tvoje", "tvého", "tvému", "tvého", "tvůj", "tvém", "tvým", "tvá", "tvých", "tvým", "tvé", "tvá", "tvých", "tvými");

        $this->vzor[$nvz++] = Array("m", "náš", "našeho", "našemu", "našeho", "náš", "našem", "našim", "naši", "našich", "našim", "naše", "naši", "našich", "našimi");
        $this->vzor[$nvz++] = Array("ž", "naše", "naší", "naší", "naši", "naše", "naší", "naší", "naše", "našich", "našim", "naše", "naše", "našich", "našimi");
        $this->vzor[$nvz++] = Array("s", "naše", "našeho", "našemu", "našeho", "naše", "našem", "našim", "naše", "našich", "našim", "naše", "naše", "našich", "našimi");

        $this->vzor[$nvz++] = Array("m", "váš", "vašeho", "vašemu", "vašeho", "váš", "vašem", "vašim", "vaši", "vašich", "vašim", "vaše", "vaši", "vašich", "vašimi");
        $this->vzor[$nvz++] = Array("ž", "vaše", "vaší", "vaší", "vaši", "vaše", "vaší", "vaší", "vaše", "vašich", "vašim", "vaše", "vaše", "vašich", "vašimi");
        $this->vzor[$nvz++] = Array("s", "vaše", "vašeho", "vašemu", "vašeho", "vaše", "vašem", "vašim", "vaše", "vašich", "vašim", "vaše", "vaše", "vašich", "vašimi");

        $this->vzor[$nvz++] = Array("m", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho");
        $this->vzor[$nvz++] = Array("ž", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho");
        $this->vzor[$nvz++] = Array("s", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho", "jeho");

        $this->vzor[$nvz++] = Array("m", "její", "jejího", "jejímu", "jejího", "její", "jejím", "jejím", "její", "jejích", "jejím", "její", "její", "jejích", "jejími");
        $this->vzor[$nvz++] = Array("s", "její", "jejího", "jejímu", "jejího", "její", "jejím", "jejím", "její", "jejích", "jejím", "její", "její", "jejích", "jejími");
        $this->vzor[$nvz++] = Array("ž", "její", "její", "její", "její", "její", "její", "její", "její", "jejích", "jejím", "její", "její", "jejích", "jejími");

        $this->vzor[$nvz++] = Array("m", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich");
        $this->vzor[$nvz++] = Array("s", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich");
        $this->vzor[$nvz++] = Array("ž", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich", "jejich");


        // výjimky (zvl. běžná slova)
        $this->vzor[$nvz++] = Array("m", "-bůh", "boha", "bohu", "boha", "bože", "bohovi", "bohem", "bozi/bohové", "bohů", "bohům", "bohy", "bozi/bohové", "bozích", "bohy");
        $this->vzor[$nvz++] = Array("m", "-pan", "pana", "panu", "pana", "pane", "panu", "panem", "páni/pánové", "pánů", "pánům", "pány", "páni/pánové", "pánech", "pány");
        $this->vzor[$nvz++] = Array("s", "moře", "moře", "moři", "moře", "moře", "moři", "mořem", "moře", "moří", "mořím", "moře", "moře", "mořích", "moři");
        $this->vzor[$nvz++] = Array("-", "dveře", "", "", "", "", "", "", "dveře", "dveří", "dveřím", "dveře", "dveře", "dveřích", "dveřmi");
        $this->vzor[$nvz++] = Array("-", "housle", "", "", "", "", "", "", "housle", "houslí", "houslím", "housle", "housle", "houslích", "houslemi");
        $this->vzor[$nvz++] = Array("-", "šle", "", "", "", "", "", "", "šle", "šlí", "šlím", "šle", "šle", "šlích", "šlemi");
        $this->vzor[$nvz++] = Array("-", "muka", "", "", "", "", "", "", "muka", "muk", "mukám", "muka", "muka", "mukách", "mukami");
        $this->vzor[$nvz++] = Array("s", "ovoce", "ovoce", "ovoci", "ovoce", "ovoce", "ovoci", "ovocem", "", "", "", "", "", "", "");
        $this->vzor[$nvz++] = Array("m", "humus", "humusu", "humusu", "humus", "humuse", "humusu", "humusem", "humusy", "humusů", "humusům", "humusy", "humusy", "humusech", "humusy");
        $this->vzor[$nvz++] = Array("m", "-vztek", "vzteku", "vzteku", "vztek", "vzteku", "vzteku", "vztekem", "vzteky", "vzteků", "vztekům", "vzteky", "vzteky", "vztecích", "vzteky");
        $this->vzor[$nvz++] = Array("m", "-dotek", "doteku", "doteku", "dotek", "doteku", "doteku", "dotekem", "doteky", "doteků", "dotekům", "doteky", "doteky", "dotecích", "doteky");
        $this->vzor[$nvz++] = Array("ž", "-hra", "hry", "hře", "hru", "hro", "hře", "hrou", "hry", "her", "hrám", "hry", "hry", "hrách", "hrami");
        $this->vzor[$nvz++] = Array("m", "Zeus", "Dia", "Diovi", "Dia", "Die", "Diovi", "Diem", "Diové", "Diů", "Diům", "?", "Diové", "?", "?");
        $this->vzor[$nvz++] = Array("ž", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol");
        $this->vzor[$nvz++] = Array("m", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol", "Nikol");

        // číslovky
        $this->vzor[$nvz++] = Array("-", "-tdva", "tidvou", "tidvoum", "tdva", "tdva", "tidvou", "tidvěmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tdvě", "tidvou", "tidvěma", "tdva", "tdva", "tidvou", "tidvěmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-ttři", "titří", "titřem", "ttři", "ttři", "titřech", "titřemi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tčtyři", "tičtyřech", "tičtyřem", "tčtyři", "tčtyři", "tičtyřech", "tičtyřmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tpět", "tipěti", "tipěti", "tpět", "tpět", "tipěti", "tipěti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tšest", "tišesti", "tišesti", "tšest", "tšest", "tišesti", "tišesti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tsedm", "tisedmi", "tisedmi", "tsedm", "tsedm", "tisedmi", "tisedmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tosm", "tiosmi", "tiosmi", "tosm", "tosm", "tiosmi", "tiosmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tdevět", "tidevíti", "tidevíti", "tdevět", "tdevět", "tidevíti", "tidevíti", "?", "?", "?", "?", "?", "?", "?");

        $this->vzor[$nvz++] = Array("ž", "-jedna", "jedné", "jedné", "jednu", "jedno", "jedné", "jednou", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("m", "-jeden", "jednoho", "jednomu", "jednoho", "jeden", "jednom", "jedním", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("s", "-jedno", "jednoho", "jednomu", "jednoho", "jedno", "jednom", "jedním", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-dva", "dvou", "dvoum", "dva", "dva", "dvou", "dvěmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-dvě", "dvou", "dvoum", "dva", "dva", "dvou", "dvěmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-tři", "tří", "třem", "tři", "tři", "třech", "třemi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-čtyři", "čtyřech", "čtyřem", "čtyři", "čtyři", "čtyřech", "čtyřmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-pět", "pěti", "pěti", "pět", "pět", "pěti", "pěti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-šest", "šesti", "šesti", "šest", "šest", "šesti", "šesti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-sedm", "sedmi", "sedmi", "sedm", "sedm", "sedmi", "sedmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-osm", "osmi", "osmi", "osm", "osm", "osmi", "osmi", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-devět", "devíti", "devíti", "devět", "devět", "devíti", "devíti", "?", "?", "?", "?", "?", "?", "?");

        $this->vzor[$nvz++] = Array("-", "deset", "deseti", "deseti", "deset", "deset", "deseti", "deseti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-ná[cs]t", "ná0ti", "ná0ti", "ná0t", "náct", "ná0ti", "ná0ti", "?", "?", "?", "?", "?", "?", "?");

        $this->vzor[$nvz++] = Array("-", "-dvacet", "dvaceti", "dvaceti", "dvacet", "dvacet", "dvaceti", "dvaceti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-třicet", "třiceti", "třiceti", "třicet", "třicet", "třiceti", "třiceti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-čtyřicet", "čtyřiceti", "čtyřiceti", "čtyřicet", "čtyřicet", "čtyřiceti", "čtyřiceti", "?", "?", "?", "?", "?", "?", "?");
        $this->vzor[$nvz++] = Array("-", "-desát", "desáti", "desáti", "desát", "desát", "desáti", "desáti", "?", "?", "?", "?", "?", "?", "?");


        //
        // Spec. přídady skloňování(+předseda, srdce jako úplná výjimka)
        //
        $this->vzor[$nvz++] = Array("m", "-[i]sta", "0sty", "0stovi", "0stu", "0sto", "0stovi", "0stou", "-0sté", "0stů", "0stům", "0sty", "0sté", "0stech", "0sty");
        $this->vzor[$nvz++] = Array("m", "-[o]sta", "0sty", "0stovi", "0stu", "0sto", "0stovi", "0stou", "-0stové", "0stů", "0stům", "0sty", "0sté", "0stech", "0sty");
        $this->vzor[$nvz++] = Array("m", "-předseda", "předsedy", "předsedovi", "předsedu", "předsedo", "předsedovi", "předsedou", "předsedové", "předsedů", "předsedům", "předsedy", "předsedové", "předsedech", "předsedy");
        $this->vzor[$nvz++] = Array("m", "-srdce", "srdce", "srdi", "sdrce", "srdce", "srdci", "srdcem", "srdce", "srdcí", "srdcím", "srdce", "srdce", "srdcích", "srdcemi");
        $this->vzor[$nvz++] = Array("m", "-[db]ce", "0ce", "0ci", "0ce", "0če", "0ci", "0cem", "0ci/0cové", "0ců", "0cům", "0ce", "0ci/0cové", "0cích", "0ci");
        $this->vzor[$nvz++] = Array("m", "-[jň]ev", "0evu", "0evu", "0ev", "0eve", "0evu", "0evem", "-0evy", "0evů", "0evům", "0evy", "0evy", "0evech", "0evy");
        $this->vzor[$nvz++] = Array("m", "-[lř]ev", "0evu/0va", "0evu/0vovi", "0ev/0va", "0eve/0ve", "0evu/0vovi", "0evem/0vem", "-0evy/0vové", "0evů/0vů", "0evům/0vům", "0evy/0vy", "0evy/0vové", "0evech/0vech", "0evy/0vy");

        $this->vzor[$nvz++] = Array("m", "-ů[lz]", "o0u/o0a", "o0u/o0ovi", "ů0/o0a", "o0e", "o0u", "o0em", "o-0y/o-0ové", "o0ů", "o0ům", "o0y", "o0y/o0ové", "o0ech", "o0y");

        // výj. nůž ($this->vzor muž)
        $this->vzor[$nvz++] = Array("m", "nůž", "nože", "noži", "nůž", "noži", "noži", "nožem", "nože", "nožů", "nožům", "nože", "nože", "nožích", "noži");


        //
        // $this->vzor kolo
        //
        $this->vzor[$nvz++] = Array("s", "-[bcčdghksštvzž]lo", "0la", "0lu", "0lo", "0lo", "0lu", "0lem", "-0la", "0el", "0lům", "0la", "0la", "0lech", "0ly");
        $this->vzor[$nvz++] = Array("s", "-[bcčdnsštvzž]ko", "0ka", "0ku", "0ko", "0ko", "0ku", "0kem", "-0ka", "0ek", "0kům", "0ka", "0ka", "0cích/0kách", "0ky");
        $this->vzor[$nvz++] = Array("s", "-[bcčdksštvzž]no", "0na", "0nu", "0no", "0no", "0nu", "0nem", "-0na", "0en", "0nům", "0na", "0na", "0nech/0nách", "0ny");
        $this->vzor[$nvz++] = Array("s", "-o", "a", "u", "o", "o", "u", "em", "-a", "", "ům", "a", "a", "ech", "y");


        //
        // $this->vzor stavení
        //
        $this->vzor[$nvz++] = Array("s", "-í", "í", "í", "í", "í", "í", "ím", "-í", "í", "ím", "í", "í", "ích", "ími");
        //
        // $this->vzor děvče  (če,dě,tě,ně,pě) výj.-také sele
        //
        $this->vzor[$nvz++] = Array("s", "-[čďť][e]", "10te", "10ti", "10", "10", "10ti", "10tem", "1-ata", "1at", "1atům", "1ata", "1ata", "1atech", "1aty");
        $this->vzor[$nvz++] = Array("s", "-[pb][ě]", "10te", "10ti", "10", "10", "10ti", "10tem", "1-ata", "1at", "1atům", "1ata", "1ata", "1atech", "1aty");

        //
        // $this->vzor žena
        //
        $this->vzor[$nvz++] = Array("ž", "-[aeiouyáéíóúý]ka", "0ky", "0ce", "0ku", "0ko", "0ce", "0kou", "-0ky", "0k", "0kám", "0ky", "0ky", "0kách", "0kami");
        $this->vzor[$nvz++] = Array("ž", "-ka", "ky", "ce", "ku", "ko", "ce", "kou", "-ky", "ek", "kám", "ky", "ky", "kách", "kami");
        $this->vzor[$nvz++] = Array("ž", "-[bdghkmnptvz]ra", "0ry", "0ře", "0ru", "0ro", "0ře", "0rou", "-0ry", "0er", "0rám", "0ry", "0ry", "0rách", "0rami");
        $this->vzor[$nvz++] = Array("ž", "-ra", "ry", "ře", "ru", "ro", "ře", "rou", "-ry", "r", "rám", "ry", "ry", "rách", "rami");
        $this->vzor[$nvz++] = Array("ž", "-[tdbnvmp]a", "0y", "0ě", "0u", "0o", "0ě", "0ou", "-0y", "0", "0ám", "0y", "0y", "0ách", "0ami");
        $this->vzor[$nvz++] = Array("ž", "-cha", "chy", "še", "chu", "cho", "še", "chou", "-chy", "ch", "chám", "chy", "chy", "chách", "chami");
        $this->vzor[$nvz++] = Array("ž", "-[gh]a", "0y", "ze", "0u", "0o", "ze", "0ou", "-0y", "0", "0ám", "0y", "0y", "0ách", "0ami");
        $this->vzor[$nvz++] = Array("ž", "-ňa", "ni", "ně", "ňou", "ňo", "ni", "ňou", "-ně/ničky", "ň", "ňám", "ně/ničky", "ně/ničky", "ňách", "ňami");
        $this->vzor[$nvz++] = Array("ž", "-[šč]a", "0i", "0e", "0u", "0o", "0e", "0ou", "-0e/0i", "0", "0ám", "0e/0i", "0e/0i", "0ách", "0ami");
        $this->vzor[$nvz++] = Array("ž", "-a", "y", "e", "u", "o", "e", "ou", "-y", "", "ám", "y", "y", "ách", "ami");

        // vz. píseň
        $this->vzor[$nvz++] = Array("ž", "-eň", "ně", "ni", "eň", "ni", "ni", "ní", "-ně", "ní", "ním", "ně", "ně", "ních", "němi");
        $this->vzor[$nvz++] = Array("ž", "-oň", "oně", "oni", "oň", "oni", "oni", "oní", "-oně", "oní", "oním", "oně", "oně", "oních", "oněmi");
        $this->vzor[$nvz++] = Array("ž", "-[ě]j", "0je", "0ji", "0j", "0ji", "0ji", "0jí", "-0je", "0jí", "0jím", "0je", "0je", "0jích", "0jemi");

        //
        // $this->vzor růže
        //
        $this->vzor[$nvz++] = Array("ž", "-ev", "ve", "vi", "ev", "vi", "vi", "ví", "-ve", "ví", "vím", "ve", "ve", "vích", "vemi");
        $this->vzor[$nvz++] = Array("ž", "-ice", "ice", "ici", "ici", "ice", "ici", "icí", "-ice", "ic", "icím", "ice", "ice", "icích", "icemi");
        $this->vzor[$nvz++] = Array("ž", "-e", "e", "i", "i", "e", "i", "í", "-e", "í", "ím", "e", "e", "ích", "emi");

        //
        // $this->vzor píseň
        //
        $this->vzor[$nvz++] = Array("ž", "-[eaá][jžň]", "10e/10i", "10i", "10", "10i", "10i", "10í", "-10e/10i", "10í", "10ím", "10e", "10e", "10ích", "10emi");
        $this->vzor[$nvz++] = Array("ž", "-[eayo][š]", "10e/10i", "10i", "10", "10i", "10i", "10í", "10e/10i", "10í", "10ím", "10e", "10e", "10ích", "10emi");
        $this->vzor[$nvz++] = Array("ž", "-[íy]ň", "0ně", "0ni", "0ň", "0ni", "0ni", "0ní", "-0ně", "0ní", "0ním", "0ně", "0ně", "0ních", "0němi");
        $this->vzor[$nvz++] = Array("ž", "-[íyý]ňe", "0ně", "0ni", "0ň", "0ni", "0ni", "0ní", "-0ně", "0ní", "0ním", "0ně", "0ně", "0ních", "0němi");
        $this->vzor[$nvz++] = Array("ž", "-[ťďž]", "0e", "0i", "0", "0i", "0i", "0í", "-0e", "0í", "0ím", "0e", "0e", "0ích", "0emi");
        $this->vzor[$nvz++] = Array("ž", "-toř", "toře", "toři", "toř", "toři", "toři", "toří", "-toře", "toří", "tořím", "toře", "toře", "tořích", "tořemi");
        $this->vzor[$nvz++] = Array("ž", "-ep", "epi", "epi", "ep", "epi", "epi", "epí", "epi", "epí", "epím", "epi", "epi", "epích", "epmi");

        //
        // $this->vzor kost
        //
        $this->vzor[$nvz++] = Array("ž", "-st", "sti", "sti", "st", "sti", "sti", "stí", "-sti", "stí", "stem", "sti", "sti", "stech", "stmi");
        $this->vzor[$nvz++] = Array("ž", "ves", "vsi", "vsi", "ves", "vsi", "vsi", "vsí", "vsi", "vsí", "vsem", "vsi", "vsi", "vsech", "vsemi");

        //
        //
        // $this->vzor Amadeus, Celsius, Kumulus, rektikulum, praktikum
        //
        $this->vzor[$nvz++] = Array("m", "-[e]us", "0a", "0u/0ovi", "0a", "0e", "0u/0ovi", "0em", "0ové", "0ů", "0ům", "0y", "0ové", "0ích", "0y");
        $this->vzor[$nvz++] = Array("m", "-[i]us", "0a", "0u/0ovi", "0a", "0e", "0u/0ovi", "0em", "0ové", "0ů", "0ům", "0usy", "0ové", "0ích", "0usy");
        $this->vzor[$nvz++] = Array("m", "-[i]s", "0se", "0su/0sovi", "0se", "0se/0si", "0su/0sovi", "0sem", "0sy/0sové", "0sů", "0sům", "0sy", "0sy/0ové", "0ech", "0sy");
        $this->vzor[$nvz++] = Array("m", "výtrus", "výtrusu", "výtrusu", "výtrus", "výtruse", "výtrusu", "výtrusem", "výtrusy", "výtrusů", "výtrusům", "výtrusy", "výtrusy", "výtrusech", "výtrusy");
        $this->vzor[$nvz++] = Array("m", "trus", "trusu", "trusu", "trus", "truse", "trusu", "trusem", "trusy", "trusů", "trusům", "trusy", "trusy", "trusech", "trusy");
        $this->vzor[$nvz++] = Array("m", "-[aeioumpts][lnmrktp]us", "10u/10a", "10u/10ovi", "10us/10a", "10e", "10u/10ovi", "10em", "10y/10ové", "10ů", "10ům", "10y", "10y/10ové", "10ech", "10y");
        $this->vzor[$nvz++] = Array("s", "-[l]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0", "0ům", "0a", "0a", "0ech", "0y");
        $this->vzor[$nvz++] = Array("s", "-[k]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0", "0ům", "0a", "0a", "0cích", "0y");
        $this->vzor[$nvz++] = Array("s", "-[i]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0í", "0ům", "0a", "0a", "0iích", "0y");
        $this->vzor[$nvz++] = Array("s", "-[i]um", "0a", "0u", "0um", "0um", "0u", "0em", "0a", "0ejí", "0ům", "0a", "0a", "0ejích", "0y");
        $this->vzor[$nvz++] = Array("s", "-io", "0a", "0u", "0", "0", "0u", "0em", "0a", "0í", "0ům", "0a", "0a", "0iích", "0y");

        //
        // $this->vzor sedlák
        //

        $this->vzor[$nvz++] = Array("m", "-[aeiouyáéíóúý]r", "0ru/0ra", "0ru/0rovi", "0r/0ra", "0re", "0ru/0rovi", "0rem", "-0ry/-0rové", "0rů", "0rům", "0ry", "0ry/0rové", "0rech", "0ry");
        // $this->vzor[$nvz++] = Array( "m","-[aeiouyáéíóúý]r","0ru/0ra","0ru/0rovi","0r/0ra","0re","0ru/0rovi","0rem",     "-0ry/-0ři","0rů","0rům","0ry","0ry/0ři", "0rech","0ry" );
        $this->vzor[$nvz++] = Array("m", "-r", "ru/ra", "ru/rovi", "r/ra", "ře", "ru/rovi", "rem", "-ry/-rové", "rů", "rům", "ry", "ry/rové", "rech", "ry");
        // $this->vzor[$nvz++] = Array( "m","-r",              "ru/ra",  "ru/rovi",  "r/ra",  "ře", "ru/rovi",   "rem",     "-ry/-ři", "rů","rům","ry",    "ry/ři",  "rech", "ry" );
        $this->vzor[$nvz++] = Array("m", "-[mnp]en", "0enu/0ena", "0enu/0enovi", "0en/0na", "0ene", "0enu/0enovi", "0enem", "-0eny/0enové", "0enů", "0enům", "0eny", "0eny/0enové", "0enech", "0eny");
        $this->vzor[$nvz++] = Array("m", "-[bcčdstvz]en", "0nu/0na", "0nu/0novi", "0en/0na", "0ne", "0nu/0novi", "0nem", "-0ny/0nové", "0nů", "0nům", "0ny", "0ny/0nové", "0nech", "0ny");
        $this->vzor[$nvz++] = Array("m", "-[dglmnpbtvzs]", "0u/0a", "0u/0ovi", "0/0a", "0e", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "0ech", "0y");
        $this->vzor[$nvz++] = Array("m", "-[x]", "0u/0e", "0u/0ovi", "0/0e", "0i", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "0ech", "0y");
        $this->vzor[$nvz++] = Array("m", "sek", "seku/seka", "seku/sekovi", "sek/seka", "seku", "seku/sekovi", "sekem", "seky/sekové", "seků", "sekům", "seky", "seky/sekové", "secích", "seky");
        $this->vzor[$nvz++] = Array("m", "výsek", "výseku/výseka", "výseku/výsekovi", "výsek/výseka", "výseku", "výseku/výsekovi", "výsekem", "výseky/výsekové", "výseků", "výsekům", "výseky", "výseky/výsekové", "výsecích", "výseky");
        $this->vzor[$nvz++] = Array("m", "zásek", "záseku/záseka", "záseku/zásekovi", "zásek/záseka", "záseku", "záseku/zásekovi", "zásekem", "záseky/zásekové", "záseků", "zásekům", "záseky", "záseky/zásekové", "zásecích", "záseky");
        $this->vzor[$nvz++] = Array("m", "průsek", "průseku/průseka", "průseku/průsekovi", "průsek/průseka", "průseku", "průseku/průsekovi", "průsekem", "průseky/průsekové", "průseků", "výsekům", "průseky", "průseky/průsekové", "průsecích", "průseky");
        $this->vzor[$nvz++] = Array("m", "-[cčšždnňmpbrstvz]ek", "0ku/0ka", "0ku/0kovi", "0ek/0ka", "0ku", "0ku/0kovi", "0kem", "-0ky/0kové", "0ků", "0kům", "0ky", "0ky/0kové", "0cích", "0ky");
        $this->vzor[$nvz++] = Array("m", "-[k]", "0u/0a", "0u/0ovi", "0/0a", "0u", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "cích", "0y");
        $this->vzor[$nvz++] = Array("m", "-ch", "chu/cha", "chu/chovi", "ch/cha", "chu/cha", "chu/chovi", "chem", "-chy/chové", "chů", "chům", "chy", "chy/chové", "ších", "chy");
        $this->vzor[$nvz++] = Array("m", "-[h]", "0u/0a", "0u/0ovi", "0/0a", "0u", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "zích", "0y");
        $this->vzor[$nvz++] = Array("m", "-e[mnz]", "0u/0a", "0u/0ovi", "e0/e0a", "0e", "0u/0ovi", "0em", "-0y/0ové", "0ů", "0ům", "0y", "0y/0ové", "0ech", "0y");

        //
        //
        // $this->vzor muž
        //
        $this->vzor[$nvz++] = Array("m", "-ec", "ce", "ci/covi", "ec/ce", "če", "ci/covi", "cem", "-ce/cové", "ců", "cům", "ce", "ce/cové", "cích", "ci");
        $this->vzor[$nvz++] = Array("m", "-[cčďšňřťž]", "0e", "0i/0ovi", "0e", "0i", "0i/0ovi", "0em", "-0e/0ové", "0ů", "0ům", "0e", "0e/0ové", "0ích", "0i");
        $this->vzor[$nvz++] = Array("m", "-oj", "oje", "oji/ojovi", "oj/oje", "oji", "oji/ojovi", "ojem", "-oje/ojové", "ojů", "ojům", "oje", "oje/ojové", "ojích", "oji");

        // $this->vzory pro přetypování rodu
        $this->vzor[$nvz++] = Array("m", "-[gh]a", "0y", "0ovi", "0u", "0o", "0ovi", "0ou", "0ové", "0ů", "0ům", "0y", "0ové", "zích", "0y");
        $this->vzor[$nvz++] = Array("m", "-[k]a", "0y", "0ovi", "0u", "0o", "0ovi", "0ou", "0ové", "0ů", "0ům", "0y", "0ové", "cích", "0y");
        $this->vzor[$nvz++] = Array("m", "-a", "y", "ovi", "u", "o", "ovi", "ou", "ové", "ů", "ům", "y", "ové", "ech", "y");

        $this->vzor[$nvz++] = Array("ž", "-l", "le", "li", "l", "li", "li", "lí", "le", "lí", "lím", "le", "le", "lích", "lemi");
        $this->vzor[$nvz++] = Array("ž", "-í", "í", "í", "í", "í", "í", "í", "í", "ích", "ím", "í", "í", "ích", "ími");
        $this->vzor[$nvz++] = Array("ž", "-[jř]", "0e", "0i", "0", "0i", "0i", "0í", "0e", "0í", "0ím", "0e", "0e", "0ích", "0emi");
        $this->vzor[$nvz++] = Array("ž", "-[č]", "0i", "0i", "0", "0i", "0i", "0í", "0i", "0í", "0ím", "0i", "0i", "0ích", "0mi");
        $this->vzor[$nvz++] = Array("ž", "-[š]", "0i", "0i", "0", "0i", "0i", "0í", "0i", "0í", "0ím", "0i", "0i", "0ích", "0emi");

        $this->vzor[$nvz++] = Array("s", "-[sljřň]e", "0ete", "0eti", "0e", "0e", "0eti", "0etem", "0ata", "0at", "0atům", "0ata", "0ata", "0atech", "0aty");
        // $this->vzor[$nvz++] = Array( "ž","-cí",        "cí", "cí",  "cí", "cí", "cí", "cí",   "cí", "cích", "cím", "cí", "cí", "cích", "cími" );
        // čaj, prodej, Ondřej, žokej
        $this->vzor[$nvz++] = Array("m", "-j", "je", "ji", "j", "ji", "ji", "jem", "je/jové", "jů", "jům", "je", "je/jové", "jích", "ji");
        // Josef, Detlef, ... ?
        $this->vzor[$nvz++] = Array("m", "-f", "fa", "fu/fovi", "f/fa", "fe", "fu/fovi", "fem", "fy/fové", "fů", "fům", "fy", "fy/fové", "fech", "fy");
        // zbroj, výzbroj, výstroj, trofej, neteř
        // jiří, podkoní, ... ?
        $this->vzor[$nvz++] = Array("m", "-í", "ího", "ímu", "ího", "í", "ímu", "ím", "í", "ích", "ím", "í", "í", "ích", "ími");
        // Hugo
        $this->vzor[$nvz++] = Array("m", "-go", "a", "govi", "ga", "ga", "govi", "gem", "gové", "gů", "gům", "gy", "gové", "zích", "gy");
        // Kvido
        $this->vzor[$nvz++] = Array("m", "-o", "a", "ovi", "a", "a", "ovi", "em", "ové", "ů", "ům", "y", "ové", "ech", "y");


        // doplňky
        // některá pomnožná jména
        $this->vzor[$nvz++] = Array("?", "-[tp]y", "?", "?", "?", "?", "?", "?", "-0y", "0", "0ům", "0y", "0y", "0ech", "0ami");
        $this->vzor[$nvz++] = Array("?", "-[k]y", "?", "?", "?", "?", "?", "?", "-0y", "e0", "0ám", "0y", "0y", "0ách", "0ami");

        // změny rodu
        $this->vzor[$nvz++] = Array("ž", "-ar", "ary", "aře", "ar", "ar", "ar", "ar", "ary", "ar", "arám", "ary", "ary", "arách", "arami");
        $this->vzor[$nvz++] = Array("ž", "-am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am", "am");
        $this->vzor[$nvz++] = Array("ž", "-er", "er", "er", "er", "er", "er", "er", "ery", "er", "erám", "ery", "ery", "erách", "erami");

        $this->vzor[$nvz++] = Array("m", "-oe", "oema", "oemovi", "oema", "oeme", "emovi", "emem", "oemové", "oemů", "oemům", "oemy", "oemové", "oemech", "oemy");

        $this->aCmpReg = Array();
        $nCmpReg = 0;
        $this->aCmpReg[0] = "";
        $this->aCmpReg[1] = "";
        $this->aCmpReg[2] = "";
        $this->aCmpReg[3] = "";
        $this->aCmpReg[4] = "";
        $this->aCmpReg[5] = "";
        $this->aCmpReg[6] = "";
        $this->aCmpReg[7] = "";
        $this->aCmpReg[8] = "";
        $this->aCmpReg[9] = "";

        //  Výjimky:
        //  $this->v1 - přehlásky
        // :  důl ... dol, stůl ... stol, nůž ... nož, hůl ... hole, půl ... půle
        $nv1 = 0;
        $this->v1 = Array();
        //                      1.p   náhrada   4.p.
        //
        $this->v1[$nv1++] = Array("osel", "osl", "osla");
        $this->v1[$nv1++] = Array("karel", "karl", "karla");
        $this->v1[$nv1++] = Array("Karel", "Karl", "Karla");
        $this->v1[$nv1++] = Array("pavel", "pavl", "pavla");
        $this->v1[$nv1++] = Array("Pavel", "Pavl", "Pavla");
        $this->v1[$nv1++] = Array("Havel", "Havl", "Havla");
        $this->v1[$nv1++] = Array("havel", "havl", "havla");
        $this->v1[$nv1++] = Array("Bořek", "Bořk", "Bořka");
        $this->v1[$nv1++] = Array("bořek", "bořk", "bořka");
        $this->v1[$nv1++] = Array("Luděk", "Luďk", "Luďka");
        $this->v1[$nv1++] = Array("luděk", "luďk", "luďka");
        $this->v1[$nv1++] = Array("pes", "ps", "psa");
        $this->v1[$nv1++] = Array("pytel", "pytl", "pytel");
        $this->v1[$nv1++] = Array("ocet", "oct", "octa");
        $this->v1[$nv1++] = Array("chléb", "chleb", "chleba");
        $this->v1[$nv1++] = Array("chleba", "chleb", "chleba");
        $this->v1[$nv1++] = Array("pavel", "pavl", "pavla");
        $this->v1[$nv1++] = Array("kel", "kl", "kel");
        $this->v1[$nv1++] = Array("sopel", "sopl", "sopel");
        $this->v1[$nv1++] = Array("kotel", "kotl", "kotel");
        $this->v1[$nv1++] = Array("posel", "posl", "posla");
        $this->v1[$nv1++] = Array("důl", "dol", "důl");
        $this->v1[$nv1++] = Array("sůl", "sole", "sůl");
        $this->v1[$nv1++] = Array("vůl", "vol", "vola");
        $this->v1[$nv1++] = Array("půl", "půle", "půli");
        $this->v1[$nv1++] = Array("hůl", "hole", "hůl");
        $this->v1[$nv1++] = Array("stůl", "stol", "stůl");
        $this->v1[$nv1++] = Array("líh", "lih", "líh");
        $this->v1[$nv1++] = Array("sníh", "sněh", "sníh");
        $this->v1[$nv1++] = Array("zář", "záře", "zář");
        $this->v1[$nv1++] = Array("svatozář", "svatozáře", "svatozář");
        $this->v1[$nv1++] = Array("kůň", "koň", "koně");
        $this->v1[$nv1++] = Array("tůň", "tůňe", "tůň");
        // --- !
        $this->v1[$nv1++] = Array("prsten", "prstýnek", "prstýnku");
        $this->v1[$nv1++] = Array("smrt", "smrť", "smrt");
        $this->v1[$nv1++] = Array("vítr", "větr", "vítr");
        $this->v1[$nv1++] = Array("stupeň", "stupň", "stupeň");
        $this->v1[$nv1++] = Array("peň", "pň", "peň");
        $this->v1[$nv1++] = Array("cyklus", "cykl", "cyklus");
        $this->v1[$nv1++] = Array("dvůr", "dvor", "dvůr");
        $this->v1[$nv1++] = Array("zeď", "zď", "zeď");
        $this->v1[$nv1++] = Array("účet", "účt", "účet");
        $this->v1[$nv1++] = Array("mráz", "mraz", "mráz");
        $this->v1[$nv1++] = Array("hnůj", "hnoj", "hnůj");
        $this->v1[$nv1++] = Array("skrýš", "skrýše", "skrýš");
        $this->v1[$nv1++] = Array("nehet", "neht", "nehet");
        $this->v1[$nv1++] = Array("veš", "vš", "veš");
        $this->v1[$nv1++] = Array("déšť", "dešť", "déšť");
        $this->v1[$nv1++] = Array("myš", "myše", "myš");

        // $this->v10 - zmena rodu na muzsky
        $this->v10 = Array();
        $nv10 = 0;
        $this->v10[$nv10++] = "sleď";
        $this->v10[$nv10++] = "saša";
        $this->v10[$nv10++] = "Saša";
        $this->v10[$nv10++] = "dešť";
        $this->v10[$nv10++] = "koň";
        $this->v10[$nv10++] = "chlast";
        $this->v10[$nv10++] = "plast";
        $this->v10[$nv10++] = "termoplast";
        $this->v10[$nv10++] = "vězeň";
        $this->v10[$nv10++] = "sťežeň";
        $this->v10[$nv10++] = "papež";
        $this->v10[$nv10++] = "ďeda";
        $this->v10[$nv10++] = "zeť";
        $this->v10[$nv10++] = "háj";
        $this->v10[$nv10++] = "lanýž";
        $this->v10[$nv10++] = "sluha";
        $this->v10[$nv10++] = "muž";
        $this->v10[$nv10++] = "velmož";
        $this->v10[$nv10++] = "Maťej";
        $this->v10[$nv10++] = "maťej";
        $this->v10[$nv10++] = "táta";
        $this->v10[$nv10++] = "kolega";
        $this->v10[$nv10++] = "mluvka";
        $this->v10[$nv10++] = "strejda";
        $this->v10[$nv10++] = "polda";
        $this->v10[$nv10++] = "moula";
        $this->v10[$nv10++] = "šmoula";
        $this->v10[$nv10++] = "slouha";
        $this->v10[$nv10++] = "drákula";
        $this->v10[$nv10++] = "test";
        $this->v10[$nv10++] = "rest";
        $this->v10[$nv10++] = "trest";
        $this->v10[$nv10++] = "arest";
        $this->v10[$nv10++] = "azbest";
        $this->v10[$nv10++] = "ametyst";
        $this->v10[$nv10++] = "chřest";
        $this->v10[$nv10++] = "protest";
        $this->v10[$nv10++] = "kontest";
        $this->v10[$nv10++] = "motorest";
        $this->v10[$nv10++] = "most";
        $this->v10[$nv10++] = "host";
        $this->v10[$nv10++] = "kříž";
        $this->v10[$nv10++] = "stupeň";
        $this->v10[$nv10++] = "peň";
        $this->v10[$nv10++] = "čaj";
        $this->v10[$nv10++] = "prodej";
        $this->v10[$nv10++] = "výdej";
        $this->v10[$nv10++] = "výprodej";
        $this->v10[$nv10++] = "ďej";
        $this->v10[$nv10++] = "zloďej";
        $this->v10[$nv10++] = "žokej";
        $this->v10[$nv10++] = "hranostaj";
        $this->v10[$nv10++] = "dobroďej";
        $this->v10[$nv10++] = "darmoďej";
        $this->v10[$nv10++] = "čaroďej";
        $this->v10[$nv10++] = "koloďej";
        $this->v10[$nv10++] = "sprej";
        $this->v10[$nv10++] = "displej";
        $this->v10[$nv10++] = "Aleš";
        $this->v10[$nv10++] = "aleš";
        $this->v10[$nv10++] = "Ambrož";
        $this->v10[$nv10++] = "ambrož";
        $this->v10[$nv10++] = "Tomáš";
        $this->v10[$nv10++] = "Lukáš";
        $this->v10[$nv10++] = "Tobiáš";
        $this->v10[$nv10++] = "Jiří";
        $this->v10[$nv10++] = "tomáš";
        $this->v10[$nv10++] = "lukáš";
        $this->v10[$nv10++] = "tobiáš";
        $this->v10[$nv10++] = "jiří";
        $this->v10[$nv10++] = "podkoní";
        $this->v10[$nv10++] = "komoří";
        $this->v10[$nv10++] = "Jirka";
        $this->v10[$nv10++] = "jirka";
        $this->v10[$nv10++] = "Ilja";
        $this->v10[$nv10++] = "ilja";
        $this->v10[$nv10++] = "Pepa";
        $this->v10[$nv10++] = "Ondřej";
        $this->v10[$nv10++] = "ondřej";
        $this->v10[$nv10++] = "Andrej";
        $this->v10[$nv10++] = "andrej";
//  $this->v10[$nv10++] = "josef";
        $this->v10[$nv10++] = "mikuláš";
        $this->v10[$nv10++] = "Mikuláš";
        $this->v10[$nv10++] = "Mikoláš";
        $this->v10[$nv10++] = "mikoláš";
        $this->v10[$nv10++] = "Kvido";
        $this->v10[$nv10++] = "kvido";
        $this->v10[$nv10++] = "Hugo";
        $this->v10[$nv10++] = "hugo";
        $this->v10[$nv10++] = "Oto";
        $this->v10[$nv10++] = "oto";
        $this->v10[$nv10++] = "Otto";
        $this->v10[$nv10++] = "otto";
        $this->v10[$nv10++] = "Alexej";
        $this->v10[$nv10++] = "alexej";
        $this->v10[$nv10++] = "Ivo";
        $this->v10[$nv10++] = "ivo";
        $this->v10[$nv10++] = "Bruno";
        $this->v10[$nv10++] = "bruno";
        $this->v10[$nv10++] = "Alois";
        $this->v10[$nv10++] = "alois";
        $this->v10[$nv10++] = "bartoloměj";
        $this->v10[$nv10++] = "Bartoloměj";
        $this->v10[$nv10++] = "noe";
        $this->v10[$nv10++] = "Noe";

        // $this->v11 - zmena rodu na zensky
        $this->v11 = Array();
        $nv11 = 0;
        $this->v11[$nv11++] = "vš";
        $this->v11[$nv11++] = "dešť";
        $this->v11[$nv11++] = "zteč";
        $this->v11[$nv11++] = "řeč";
        $this->v11[$nv11++] = "křeč";
        $this->v11[$nv11++] = "kleč";
        $this->v11[$nv11++] = "maštal";
        $this->v11[$nv11++] = "vš";
        $this->v11[$nv11++] = "kancelář";
        $this->v11[$nv11++] = "závěj";
        $this->v11[$nv11++] = "zvěř";
        $this->v11[$nv11++] = "sbeř";
        $this->v11[$nv11++] = "neteř";
        $this->v11[$nv11++] = "ves";
        $this->v11[$nv11++] = "rozkoš";
        // $this->v11[$nv11++] = "myša";
        $this->v11[$nv11++] = "postel";
        $this->v11[$nv11++] = "prdel";
        $this->v11[$nv11++] = "koudel";
        $this->v11[$nv11++] = "koupel";
        $this->v11[$nv11++] = "ocel";
        $this->v11[$nv11++] = "digestoř";
        $this->v11[$nv11++] = "konzervatoř";
        $this->v11[$nv11++] = "oratoř";
        $this->v11[$nv11++] = "zbroj";
        $this->v11[$nv11++] = "výzbroj";
        $this->v11[$nv11++] = "výstroj";
        $this->v11[$nv11++] = "trofej";
        $this->v11[$nv11++] = "obec";
        $this->v11[$nv11++] = "otep";
        $this->v11[$nv11++] = "Miriam";
        // $this->v11[$nv11++] = "miriam";
        $this->v11[$nv11++] = "Ester";
        $this->v11[$nv11++] = "Dagmar";

        // $this->v11[$nv11++] = "transmise"
        // $this->v12 - zmena rodu na stredni
        $this->v12 = Array();
        $nv12 = 0;
        $this->v12[$nv12++] = "nemluvňe";
        $this->v12[$nv12++] = "slůně";
        $this->v12[$nv12++] = "kůzle";
        $this->v12[$nv12++] = "sele";
        $this->v12[$nv12++] = "osle";
        $this->v12[$nv12++] = "zvíře";
        $this->v12[$nv12++] = "kuře";
        $this->v12[$nv12++] = "tele";
        $this->v12[$nv12++] = "prase";
        $this->v12[$nv12++] = "house";
        $this->v12[$nv12++] = "vejce";


        // $this->v0 - nedořešené výjimky
        $this->v0 = Array();
        $nv0 = 0;
        $this->v0[$nv0++] = "sten";
//  $this->v0[nv0++] = "Ester"
//  $this->v0[nv0++] = "Dagmar"
//  $this->v0[nv0++] = "ovoce"
//  $this->v0[nv0++] = "Zeus"
//  $this->v0[nv0++] = "zbroj"
//  $this->v0[nv0++] = "výzbroj"
//  $this->v0[nv0++] = "výstroj"
//  $this->v0[nv0++] = "obec"
//  $this->v0[nv0++] = "konzervatoř"
//  $this->v0[nv0++] = "digestoř"
//  $this->v0[nv0++] = "humus"
//  $this->v0[nv0++] = "muka"
//  $this->v0[nv0++] = "noe"
//  $this->v0[nv0++] = "Noe"
        // $this->v0[nv0++] = "Miriam"
        // $this->v0[nv0++] = "miriam"
        // Je Nikola ženské nebo mužské jméno??? (podobně Sáva)
        // $this->v3 - různé odchylky ve skloňování
        //    - časem by bylo vhodné opravit
        $nv3 = 0;
        $this->v3 = Array();
        $this->v3[$nv3++] = "jméno";
        $this->v3[$nv3++] = "myš";
        $this->v3[$nv3++] = "vězeň";
        $this->v3[$nv3++] = "sťežeň";
        $this->v3[$nv3++] = "oko";
        $this->v3[$nv3++] = "sole";
        $this->v3[$nv3++] = "šach";
        $this->v3[$nv3++] = "veš";
        $this->v3[$nv3++] = "myš";
        $this->v3[$nv3++] = "klášter";
        $this->v3[$nv3++] = "kněz";
        $this->v3[$nv3++] = "král";
        $this->v3[$nv3++] = "zď";
        $this->v3[$nv3++] = "sto";
        $this->v3[$nv3++] = "smrt";
        $this->v3[$nv3++] = "leden";
        $this->v3[$nv3++] = "len";
        $this->v3[$nv3++] = "les";
        $this->v3[$nv3++] = "únor";
        $this->v3[$nv3++] = "březen";
        $this->v3[$nv3++] = "duben";
        $this->v3[$nv3++] = "květen";
        $this->v3[$nv3++] = "červen";
        $this->v3[$nv3++] = "srpen";
        $this->v3[$nv3++] = "říjen";
        $this->v3[$nv3++] = "pantofel";
        $this->v3[$nv3++] = "žába";
        $this->v3[$nv3++] = "zoja";
        $this->v3[$nv3++] = "Zoja";
        $this->v3[$nv3++] = "Zoe";
        $this->v3[$nv3++] = "zoe";

// Ve zvl. pripadech je mozne pomoci teto promenne "pretypovat" rod jmena
        $this->PrefRod = "0"; // smi byt "0", "m", "ž", "s"


        $this->astrTvar = Array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
    }

//
//  Fce isShoda vraci index pri shode koncovky (napr. isShoda("-lo","kolo"), isShoda("ko-lo","motovidlo"))
//  nebo pri rovnosti slov (napr. isShoda("molo","molo").
//  Jinak je navratova hodnota -1.
//
    private function isShoda($vz, $txt)
    {
        $txt = mb_strtolower($txt, 'UTF-8');
        $vz = mb_strtolower($vz, 'UTF-8');;
        $i = mb_strlen($vz, 'UTF-8');
        $j = mb_strlen($txt, 'UTF-8');

        if ($i == 0 || $j == 0)
            return -1;
        $i--;
        $j--;

        $nCmpReg = 0;

        while ($i >= 0 && $j >= 0) {
            if (mb_substr($vz, $i, 1, 'UTF-8') == "]") {
                $i--;
                $quit = 1;
                while ($i >= 0 && mb_substr($vz, $i, 1, 'UTF-8') != "[") {
                    if (mb_substr($vz, $i, 1, 'UTF-8') == mb_substr($txt, $j, 1, 'UTF-8')) {
                        $quit = 0;
                        $this->aCmpReg[$nCmpReg] = mb_substr($vz, $i, 1, 'UTF-8');
                        $nCmpReg++;
                    }
                    $i--;
                }

                if ($quit == 1)
                    return -1;
            } else {
                if (mb_substr($vz, $i, 1, 'UTF-8') == '-')
                    return $j + 1;
                if (mb_substr($vz, $i, 1, 'UTF-8') != mb_substr($txt, $j, 1, 'UTF-8'))
                    return -1;
            }
            $i--;
            $j--;
        }
        if ($i < 0 && $j < 0)
            return 0;
        if (mb_substr($vz, $i, 1, 'UTF-8') == '-')
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
        for ($XdeteneI = 0; $XdeteneI < mb_strlen($txt2, 'UTF-8') - 1; $XdeteneI++) {
            if (mb_substr($txt2, $XdeteneI, 1, 'UTF-8') == "ď" && (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "e" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "i" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "í")) {
                $XdeteneRV .= "d";
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "e") {
                    $XdeteneRV .= "ě";
                    $XdeteneI++;
                }
            } else if (mb_substr($txt2, $XdeteneI, 1, 'UTF-8') == "ť" && (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "e" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "i" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "í")) {
                $XdeteneRV .= "t";
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "e") {
                    $XdeteneRV .= "ě";
                    $XdeteneI++;
                }
            } else if (mb_substr($txt2, $XdeteneI, 1, 'UTF-8') == "ň" && (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "e" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "i" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "í")) {
                $XdeteneRV .= "n";
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "e") {
                    $XdeteneRV .= "ě";
                    $XdeteneI++;
                }
            } else
                $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
        }

        if ($XdeteneI == mb_strlen($txt2, 'UTF-8') - 1)
            $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');

        return $XdeteneRV;
    }

//
// Transformace: di,ti,ni,dě,tě,ně ... ďi,ťi,ňi,ďe,ťe,ňe
//
    private function Xedeten($txt2)
    {
        $XdeteneRV = "";
        for ($XdeteneI = 0; $XdeteneI < mb_strlen($txt2, 'UTF-8') - 1; $XdeteneI++) {
            if (mb_substr($txt2, $XdeteneI, 1, 'UTF-8') == "d" && (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "ě" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "i")) {
                $XdeteneRV .= "ď";
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "ě") {
                    $XdeteneRV .= "e";
                    $XdeteneI++;
                }
            } else if (mb_substr($txt2, $XdeteneI, 1, 'UTF-8') == "t" && (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "ě" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "i")) {
                $XdeteneRV .= "ť";
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "ě") {
                    $XdeteneRV .= "e";
                    $XdeteneI++;
                }
            } else if (mb_substr($txt2, $XdeteneI, 1, 'UTF-8') == "n" && (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "ě" || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "i")) {
                $XdeteneRV .= "ň";
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') == "ě") {
                    $XdeteneRV .= "e";
                    $XdeteneI++;
                }
            } else
                $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
        }

        if ($XdeteneI == mb_strlen($txt2, 'UTF-8') - 1)
            $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');

        return $XdeteneRV;
    }

//
// Funkce pro sklonovani
//

    private function CmpFrm($txt)
    {
        $CmpFrmRV = "";
        for ($CmpFrmI = 0; $CmpFrmI < mb_strlen($txt, 'UTF-8'); $CmpFrmI++)
            if (mb_substr($txt, $CmpFrmI, 1, 'UTF-8') == "0")
                $CmpFrmRV .= $this->aCmpReg[0];
            else if (mb_substr($txt, $CmpFrmI, 1, 'UTF-8') == "1")
                $CmpFrmRV .= $this->aCmpReg[1];
            else if (mb_substr($txt, $CmpFrmI, 1, 'UTF-8') == "2")
                $CmpFrmRV .= $this->aCmpReg[2];
            else
                $CmpFrmRV .= mb_substr($txt, $CmpFrmI, 1, 'UTF-8');

        return $CmpFrmRV;
    }

// Funkce pro sklonovani slova do daneho podle 
// daneho $this->vzoru
    private function Sklon($nPad, $vzndx, $txt, $zivotne = false)
    {

        if ($vzndx >= count($this->vzor) || $vzndx < 0)
            return "???";

        $txt3 = $this->Xedeten($txt);
        $kndx = $this->isShoda($this->vzor[$vzndx][1], $txt3);
        if ($kndx < 0 || $nPad < 1 || $nPad > 14) //8-14 je pro plural
            return "???";

        if ($this->vzor[$vzndx][$nPad] == "?")
            return "?";

        if (!$this->isDbgMode & $nPad == 1) // 1. pad nemenime
            $rv = $this->Xdetene($txt3);
        else
            $rv = $this->LeftStr($kndx, $txt3) . '-' . $this->CmpFrm($this->vzor[$vzndx][$nPad]);

        if ($this->isDbgMode) //preskoceni filtrovani
            return $rv;

        // Formatovani zivotneho sklonovani
        // - nalezeni pomlcky
        for ($nnn = 0; $nnn < mb_strlen($rv, 'UTF-8'); $nnn++)
            if (mb_substr($rv, $nnn, 1, 'UTF-8') == "-")
                break;

        $ndx1 = $nnn;

        // - nalezeni lomitka
        for ($nnn = 0; $nnn < mb_strlen($rv, 'UTF-8'); $nnn++)
            if (mb_substr($rv, $nnn, 1, 'UTF-8') == "/")
                break;

        $ndx2 = $nnn;


        if ($ndx1 != mb_strlen($rv, 'UTF-8') && $ndx2 != mb_strlen($rv, 'UTF-8')) {
            if ($zivotne)
                // "text-xxx/yyy" -> "textyyy"
                $rv = $this->LeftStr($ndx1, $rv) . $this->RightStr($ndx2 + 1, $rv);
            else
                // "text-xxx/yyy" -> "text-xxx"
                $rv = $this->LeftStr($ndx2, $rv);
        }


        // vypusteni pomocnych znaku
        $txt3 = "";
        for ($nnn = 0; $nnn < mb_strlen($rv, 'UTF-8'); $nnn++)
            if (!(mb_substr($rv, $nnn, 1, 'UTF-8') == '-' || mb_substr($rv, $nnn, 1, 'UTF-8') == '/'))
                $txt3 .= mb_substr($rv, $nnn, 1, 'UTF-8');

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
        $rv = "";
        for ($i = 0; $i < $n && $i < mb_strlen($txt, 'UTF-8'); $i++)
            $rv .= mb_substr($txt, $i, 1, 'UTF-8');

        return $rv;
    }

// - pravy retezec od indexu n (vcetne)
    private function RightStr($n, $txt)
    {
        $rv = "";
        for ($i = $n; $i < mb_strlen($txt, 'UTF-8'); $i++)
            $rv .= mb_substr($txt, $i, 1, 'UTF-8');

        return $rv;
    }

// Rozdeleni textu na slova
    private function txtSplit($txt)
    {
        $skp = 1;
        $rv = Array();

        $rvx = 0;
        $acc = "";

        for ($i = 0; $i < mb_strlen($txt, 'UTF-8'); $i++) {
            if (mb_substr($txt, $i, 1, 'UTF-8') == ' ') {
                if ($skp)
                    continue;
                $skp = 1;
                $rv[$rvx++] = $acc;
                $acc = "";
                continue;
            }
            $skp = 0;
            $acc .= mb_substr($txt, $i, 1, 'UTF-8');
        }
        if (!$skp)
            $rv[$rvx++] = $acc;

        return $rv;
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
        $aTxt = $this->txtSplit($text);

        $this->PrefRod = "0";
        $out = array();
        for ($i = count($aTxt) - 1; $i >= 0; $i--) {
            // vysklonovani
            $this->skl2($aTxt[$i], $preferovanyRod, $zivotne);

            // vynuceni rodu podle posledniho slova
            if ($i == count($aTxt) - 1)
                $this->PrefRod = $this->astrTvar[0];

            // pokud nenajdeme $this->vzor tak nesklonujeme
            if ($i < count($aTxt) - 1 && mb_substr($this->astrTvar[0], 0, 1, 'UTF-8') == '?' && mb_substr($this->PrefRod, 0, 1, 'UTF-8') != '?') {
                for ($j = 1; $j < 15; $j++)
                    $this->astrTvar[$j] = $aTxt[$i];
            }

            if (mb_substr($this->astrTvar[0], 0, 1, 'UTF-8') == '?')
                $this->astrTvar[0] = '';

            if ($i < count($aTxt)) {
                for ($j = 1; $j < 15; $j++)
                    @$out[$j] = $this->astrTvar[$j] . ' ' . @$out[$j];
            } else {
                for ($j = 1; $j < 15; $j++)
                    @$out[$j] = $this->astrTvar[$j];
            }
        }
        return $out;
    }

// Sklonovani podle standardniho seznamu pripon
    private function SklStd($slovo, $ii, $zivotne)
    {

        if ($ii < 0 || $ii > count($this->vzor))
            $this->astrTvar[0] = "!!!???";

        // - seznam nedoresenych slov
        for ($jj = 0; $jj < count($this->v0); $jj++)
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
        for ($vxi = 0; $vxi < count($vx); $vxi++)
            if ($slovo == $vx[$vxi])
                return $vxi;

        return -1;
    }

// Pokud je index>=0, je $slovo výjimka ze seznamu "$vx", definovaného výše.
    private function ndxV1($slovo)
    {
        for ($this->v1i = 0; $this->v1i < count($this->v1); $this->v1i++)
            if ($slovo == $this->v1[$this->v1i][0])
                return $this->v1i;

        return -1;
    }

    private function StdNdx($slovo)
    {
        for ($iii = 0; $iii < count($this->vzor); $iii++) {
            // filtrace rodu
            if (mb_substr($this->PrefRod, 0, 1, 'UTF-8') != "0" && mb_substr($this->PrefRod, 0, 1, 'UTF-8') != mb_substr($this->vzor[$iii][0], 0, 1, 'UTF-8'))
                continue;

            if ($this->isShoda($this->vzor[$iii][1], $slovo) >= 0)
                break;
        }

        if ($iii >= count($this->vzor))
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
        return 0; //return SklFmt( $this->astrTvar ); //  return "$this->vzor: "+$this->vzor[$ii][1];
    }

    /**
     * Try to detect female genus by given surname
     * - only basic detection
     * @author Jan Navratil <jan.navratil@heureka.cz>
     * @param $surname
     * @return null|string
     */
    public function isFemaleGenusSurname($surname)
    {
        if ('ova' == str_replace('á', 'a', mb_substr(mb_strtolower($surname), -3))) {
            return self::GENUS_FEMININE;
        } else {
            return null;
        }
    }

}