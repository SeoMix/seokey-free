<?php
/**
 * Stopwords for some audit tasks
 *
 * @Loaded on 'init' & role editor
 *
 * @see     audit.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Get stopwords for the given language
 *
 * @param  string  $language must be de 2 first letters of the locale (for exemple en for english, fr for french...)
 *
 * @return array stopwords for the given language or the default site language
 */
function seokey_get_stopwords( $language = '' ) {
    $stopwords = []; // Prepare an array for our stop words
	// If we have a language, check the stop words for this language
	if ( $language !== '' ) {
		// Get the correct stop words for our language
		switch ( $language ) {
			case 'en' : // English
				$stopwords = ["i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now"];
				break;
			case 'fr' : // French
				$stopwords = ['au', 'aux', 'avec', 'ce', 'ces', 'dans', 'de', 'des', 'du', 'elle', 'en', 'et', 'eux', 'il', 'je', 'la', 'le', 'les', 'leur', 'lui', 'ma', 'mais', 'me', 'même', 'mes', 'moi', 'mon', 'ne', 'nos', 'notre', 'nous', 'on', 'ou', 'par', 'pas', 'pour', 'qu', 'que', 'qui', 'sa', 'se', 'ses', 'son', 'sur', 'ta', 'te', 'tes', 'toi', 'ton', 'tu', 'un', 'une', 'vos', 'votre', 'vous', 'c', 'd', 'j', 'l', 'à', 'm', 'n', 's', 't', 'y', 'été', 'étée', 'étées', 'étés', 'étant', 'étante', 'étants', 'étantes', 'suis', 'es', 'est', 'sommes', 'êtes', 'sont', 'serai', 'seras', 'sera', 'serons', 'serez', 'seront', 'serais', 'serait', 'serions', 'seriez', 'seraient', 'étais', 'était', 'étions', 'étiez', 'étaient', 'fus', 'fut', 'fûmes', 'fûtes', 'furent', 'sois', 'soit', 'soyons', 'soyez', 'soient', 'fusse', 'fusses', 'fût', 'fussions', 'fussiez', 'fussent', 'ayant', 'ayante', 'ayantes', 'ayants', 'eu', 'eue', 'eues', 'eus', 'ai', 'as', 'avons', 'avez', 'ont', 'aurai', 'auras', 'aura', 'aurons', 'aurez', 'auront', 'aurais', 'aurait', 'aurions', 'auriez', 'auraient', 'avais', 'avait', 'avions', 'aviez', 'avaient', 'eut', 'eûmes', 'eûtes', 'eurent', 'aie', 'aies', 'ait', 'ayons', 'ayez', 'aient', 'eusse', 'eusses', 'eût', 'eussions', 'eussiez', 'eussent'];
				break;
			case 'it' : // Italian
				$stopwords = ['ad', 'al', 'allo', 'ai', 'agli', 'all', 'agl', 'alla', 'alle', 'con', 'col', 'coi', 'da', 'dal', 'dallo', 'dai', 'dagli', 'dall', 'dagl', 'dalla', 'dalle', 'di', 'del', 'dello', 'dei', 'degli', 'dell', 'degl', 'della', 'delle', 'in', 'nel', 'nello', 'nei', 'negli', 'nell', 'negl', 'nella', 'nelle', 'su', 'sul', 'sullo', 'sui', 'sugli', 'sull', 'sugl', 'sulla', 'sulle', 'per', 'tra', 'contro', 'io', 'tu', 'lui', 'lei', 'noi', 'voi', 'loro', 'mio', 'mia', 'miei', 'mie', 'tuo', 'tua', 'tuoi', 'tue', 'suo', 'sua', 'suoi', 'sue', 'nostro', 'nostra', 'nostri', 'nostre', 'vostro', 'vostra', 'vostri', 'vostre', 'mi', 'ti', 'ci', 'vi', 'lo', 'la', 'li', 'le', 'gli', 'ne', 'il', 'un', 'uno', 'una', 'ma', 'ed', 'se', 'perché', 'anche', 'come', 'dov', 'dove', 'che', 'chi', 'cui', 'non', 'più', 'quale', 'quanto', 'quanti', 'quanta', 'quante', 'quello', 'quelli', 'quella', 'quelle', 'questo', 'questi', 'questa', 'queste', 'si', 'tutto', 'tutti', 'a', 'c', 'e', 'i', 'l', 'o', 'ho', 'hai', 'ha', 'abbiamo', 'avete', 'hanno', 'abbia', 'abbiate', 'abbiano', 'avrò', 'avrai', 'avrà', 'avremo', 'avrete', 'avranno', 'avrei', 'avresti', 'avrebbe', 'avremmo', 'avreste', 'avrebbero', 'avevo', 'avevi', 'aveva', 'avevamo', 'avevate', 'avevano', 'ebbi', 'avesti', 'ebbe', 'avemmo', 'aveste', 'ebbero', 'avessi', 'avesse', 'avessimo', 'avessero', 'avendo', 'avuto', 'avuta', 'avuti', 'avute', 'sono', 'sei', 'è', 'siamo', 'siete', 'sia', 'siate', 'siano', 'sarò', 'sarai', 'sarà', 'saremo', 'sarete', 'saranno', 'sarei', 'saresti', 'sarebbe', 'saremmo', 'sareste', 'sarebbero', 'ero', 'eri', 'era', 'eravamo', 'eravate', 'erano', 'fui', 'fosti', 'fu', 'fummo', 'foste', 'furono', 'fossi', 'fosse', 'fossimo', 'fossero', 'essendo', 'faccio', 'fai', 'facciamo', 'fanno', 'faccia', 'facciate', 'facciano', 'farò', 'farai', 'farà', 'faremo', 'farete', 'faranno', 'farei', 'faresti', 'farebbe', 'faremmo', 'fareste', 'farebbero', 'facevo', 'facevi', 'faceva', 'facevamo', 'facevate', 'facevano', 'feci', 'facesti', 'fece', 'facemmo', 'faceste', 'fecero', 'facessi', 'facesse', 'facessimo', 'facessero', 'facendo', 'sto', 'stai', 'sta', 'stiamo', 'stanno', 'stia', 'stiate', 'stiano', 'starò', 'starai', 'starà', 'staremo', 'starete', 'staranno', 'starei', 'staresti', 'starebbe', 'staremmo', 'stareste', 'starebbero', 'stavo', 'stavi', 'stava', 'stavamo', 'stavate', 'stavano', 'stetti', 'stesti', 'stette', 'stemmo', 'steste', 'stettero', 'stessi', 'stesse', 'stessimo', 'stessero', 'stando'];
				break;
			case 'nl' : // Dutch
				$stopwords = ["aan", "achte", "achter", "af", "al", "alle", "alleen", "alles", "als", "ander", "anders", "beetje", "behalve", "beide", "beiden", "ben", "beneden", "bent", "bij", "bijna", "bijv", "blijkbaar", "blijken", "boven", "bv", "daar", "daardoor", "daarin", "daarna", "daarom", "daaruit", "dan", "dat", "de", "deden", "deed", "derde", "derhalve", "dertig", "deze", "dhr", "die", "dit", "doe", "doen", "doet", "door", "drie", "duizend", "echter", "een", "eens", "eerst", "eerste", "eigen", "eigenlijk", "elk", "elke", "en", "enige", "er", "erg", "ergens", "etc", "etcetera", "even", "geen", "genoeg", "geweest", "haar", "haarzelf", "had", "hadden", "heb", "hebben", "hebt", "hedden", "heeft", "heel", "hem", "hemzelf", "hen", "het", "hetzelfde", "hier", "hierin", "hierna", "hierom", "hij", "hijzelf", "hoe", "honderd", "hun", "ieder", "iedere", "iedereen", "iemand", "iets", "ik", "in", "inderdaad", "intussen", "is", "ja", "je", "jij", "jijzelf", "jou", "jouw", "jullie", "kan", "kon", "konden", "kun", "kunnen", "kunt", "laatst", "later", "lijken", "lijkt", "maak", "maakt", "maakte", "maakten", "maar", "mag", "maken", "me", "meer", "meest", "meestal", "men", "met", "mevr", "mij", "mijn", "minder", "miss", "misschien", "missen", "mits", "mocht", "mochten", "moest", "moesten", "moet", "moeten", "mogen", "mr", "mrs", "mw", "na", "naar", "nam", "namelijk", "nee", "neem", "negen", "nemen", "nergens", "niemand", "niet", "niets", "niks", "noch", "nochtans", "nog", "nooit", "nu", "nv", "of", "om", "omdat", "ondanks", "onder", "ondertussen", "ons", "onze", "onzeker", "ooit", "ook", "op", "over", "overal", "overige", "paar", "per", "recent", "redelijk", "samen", "sinds", "steeds", "te", "tegen", "tegenover", "thans", "tien", "tiende", "tijdens", "tja", "toch", "toe", "tot", "totdat", "tussen", "twee", "tweede", "u", "uit", "uw", "vaak", "van", "vanaf", "veel", "veertig", "verder", "verscheidene", "verschillende", "via", "vier", "vierde", "vijf", "vijfde", "vijftig", "volgend", "volgens", "voor", "voordat", "voorts", "waar", "waarom", "waarschijnlijk", "wanneer", "waren", "was", "wat", "we", "wederom", "weer", "weinig", "wel", "welk", "welke", "werd", "werden", "werder", "whatever", "wie", "wij", "wijzelf", "wil", "wilden", "willen", "word", "worden", "wordt", "zal", "ze", "zei", "zeker", "zelf", "zelfde", "zes", "zeven", "zich", "zij", "zijn", "zijzelf", "zo", "zoals", "zodat", "zou", "zouden", "zulk", "zullen"];
				break;
			case 'de' : // German
				$stopwords = ['a', 'ab', 'aber', 'aber', 'ach', 'acht', 'achte', 'achten', 'achter', 'achtes', 'ag', 'alle', 'allein', 'allem', 'allen', 'aller', 'allerdings', 'alles', 'allgemeinen', 'als', 'als', 'also', 'am', 'an', 'andere', 'anderen', 'andern', 'anders', 'au', 'auch', 'auch', 'auf', 'aus', 'ausser', 'außer', 'ausserdem', 'außerdem', 'b', 'bald', 'bei', 'beide', 'beiden', 'beim', 'beispiel', 'bekannt', 'bereits', 'besonders', 'besser', 'besten', 'bin', 'bis', 'bisher', 'bist', 'c', 'd', 'da', 'dabei', 'dadurch', 'dafür', 'dagegen', 'daher', 'dahin', 'dahinter', 'damals', 'damit', 'danach', 'daneben', 'dank', 'dann', 'daran', 'darauf', 'daraus', 'darf', 'darfst', 'darin', 'darüber', 'darum', 'darunter', 'das', 'das', 'dasein', 'daselbst', 'dass', 'daß', 'dasselbe', 'davon', 'davor', 'dazu', 'dazwischen', 'dein', 'deine', 'deinem', 'deiner', 'dem', 'dementsprechend', 'demgegenüber', 'demgemäss', 'demgemäß', 'demselben', 'demzufolge', 'den', 'denen', 'denn', 'denn', 'denselben', 'der', 'deren', 'derjenige', 'derjenigen', 'dermassen', 'dermaßen', 'derselbe', 'derselben', 'des', 'deshalb', 'desselben', 'dessen', 'deswegen', 'd.h', 'dich', 'die', 'diejenige', 'diejenigen', 'dies', 'diese', 'dieselbe', 'dieselben', 'diesem', 'diesen', 'dieser', 'dieses', 'dir', 'doch', 'dort', 'drei', 'drin', 'dritte', 'dritten', 'dritter', 'drittes', 'du', 'durch', 'durchaus', 'dürfen', 'dürft', 'durfte', 'durften', 'e', 'eben', 'ebenso', 'ehrlich', 'ei', 'ei,', 'ei,', 'eigen', 'eigene', 'eigenen', 'eigener', 'eigenes', 'ein', 'einander', 'eine', 'einem', 'einen', 'einer', 'eines', 'einige', 'einigen', 'einiger', 'einiges', 'einmal', 'einmal', 'eins', 'elf', 'en', 'ende', 'endlich', 'entweder', 'entweder', 'er', 'Ernst', 'erst', 'erste', 'ersten', 'erster', 'erstes', 'es', 'etwa', 'etwas', 'euch', 'f', 'früher', 'fünf', 'fünfte', 'fünften', 'fünfter', 'fünftes', 'für', 'g', 'gab', 'ganz', 'ganze', 'ganzen', 'ganzer', 'ganzes', 'gar', 'gedurft', 'gegen', 'gegenüber', 'gehabt', 'gehen', 'geht', 'gekannt', 'gekonnt', 'gemacht', 'gemocht', 'gemusst', 'genug', 'gerade', 'gern', 'gesagt', 'gesagt', 'geschweige', 'gewesen', 'gewollt', 'geworden', 'gibt', 'ging', 'gleich', 'gott', 'gross', 'groß', 'grosse', 'große', 'grossen', 'großen', 'grosser', 'großer', 'grosses', 'großes', 'gut', 'gute', 'guter', 'gutes', 'h', 'habe', 'haben', 'habt', 'hast', 'hat', 'hatte', 'hätte', 'hatten', 'hätten', 'heisst', 'her', 'heute', 'hier', 'hin', 'hinter', 'hoch', 'i', 'ich', 'ihm', 'ihn', 'ihnen', 'ihr', 'ihre', 'ihrem', 'ihren', 'ihrer', 'ihres', 'im', 'im', 'immer', 'in', 'in', 'indem', 'infolgedessen', 'ins', 'irgend', 'ist', 'j', 'ja', 'ja', 'jahr', 'jahre', 'jahren', 'je', 'jede', 'jedem', 'jeden', 'jeder', 'jedermann', 'jedermanns', 'jedoch', 'jemand', 'jemandem', 'jemanden', 'jene', 'jenem', 'jenen', 'jener', 'jenes', 'jetzt', 'k', 'kam', 'kann', 'kannst', 'kaum', 'kein', 'keine', 'keinem', 'keinen', 'keiner', 'kleine', 'kleinen', 'kleiner', 'kleines', 'kommen', 'kommt', 'können', 'könnt', 'konnte', 'könnte', 'konnten', 'kurz', 'l', 'lang', 'lange', 'lange', 'leicht', 'leide', 'lieber', 'los', 'm', 'machen', 'macht', 'machte', 'mag', 'magst', 'mahn', 'man', 'manche', 'manchem', 'manchen', 'mancher', 'manches', 'mann', 'mehr', 'mein', 'meine', 'meinem', 'meinen', 'meiner', 'meines', 'mensch', 'menschen', 'mich', 'mir', 'mit', 'mittel', 'mochte', 'möchte', 'mochten', 'mögen', 'möglich', 'mögt', 'morgen', 'muss', 'muß', 'müssen', 'musst', 'müsst', 'musste', 'mussten', 'n', 'na', 'nach', 'nachdem', 'nahm', 'natürlich', 'neben', 'nein', 'neue', 'neuen', 'neun', 'neunte', 'neunten', 'neunter', 'neuntes', 'nicht', 'nicht', 'nichts', 'nie', 'niemand', 'niemandem', 'niemanden', 'noch', 'nun', 'nun', 'nur', 'o', 'ob', 'ob', 'oben', 'oder', 'oder', 'offen', 'oft', 'oft', 'ohne', 'Ordnung', 'p', 'q', 'r', 'recht', 'rechte', 'rechten', 'rechter', 'rechtes', 'richtig', 'rund', 's', 'sa', 'sache', 'sagt', 'sagte', 'sah', 'satt', 'schlecht', 'Schluss', 'schon', 'sechs', 'sechste', 'sechsten', 'sechster', 'sechstes', 'sehr', 'sei', 'sei', 'seid', 'seien', 'sein', 'seine', 'seinem', 'seinen', 'seiner', 'seines', 'seit', 'seitdem', 'selbst', 'selbst', 'sich', 'sie', 'sieben', 'siebente', 'siebenten', 'siebenter', 'siebentes', 'sind', 'so', 'solang', 'solche', 'solchem', 'solchen', 'solcher', 'solches', 'soll', 'sollen', 'sollte', 'sollten', 'sondern', 'sonst', 'sowie', 'später', 'statt', 't', 'tag', 'tage', 'tagen', 'tat', 'teil', 'tel', 'tritt', 'trotzdem', 'tun', 'u', 'über', 'überhaupt', 'übrigens', 'uhr', 'um', 'und', 'und', 'uns', 'unser', 'unsere', 'unserer', 'unter', 'v', 'vergangenen', 'viel', 'viele', 'vielem', 'vielen', 'vielleicht', 'vier', 'vierte', 'vierten', 'vierter', 'viertes', 'vom', 'von', 'vor', 'w', 'wahr', 'während', 'währenddem', 'währenddessen', 'wann', 'war', 'wäre', 'waren', 'wart', 'warum', 'was', 'wegen', 'weil', 'weit', 'weiter', 'weitere', 'weiteren', 'weiteres', 'welche', 'welchem', 'welchen', 'welcher', 'welches', 'wem', 'wen', 'wenig', 'wenig', 'wenige', 'weniger', 'weniges', 'wenigstens', 'wenn', 'wenn', 'wer', 'werde', 'werden', 'werdet', 'wessen', 'wie', 'wie', 'wieder', 'will', 'willst', 'wir', 'wird', 'wirklich', 'wirst', 'wo', 'wohl', 'wollen', 'wollt', 'wollte', 'wollten', 'worden', 'wurde', 'würde', 'wurden', 'würden', 'x', 'y', 'z', 'z.b', 'zehn', 'zehnte', 'zehnten', 'zehnter', 'zehntes', 'zeit', 'zu', 'zuerst', 'zugleich', 'zum', 'zum', 'zunächst', 'zur', 'zurück', 'zusammen', 'zwanzig', 'zwar', 'zwar', 'zwei', 'zweite', 'zweiten', 'zweiter', 'zweites', 'zwischen', 'zwölf'];
				break;
			case 'cs' : // Czech
				$stopwords = ['a', 'aby', 'aj', 'ale', 'ani', 'aniž', 'ano', 'asi', 'až', 'bez', 'bude', 'budem', 'budeš', 'by', 'byl', 'byla', 'byli', 'bylo', 'být', 'co', 'což', 'cz', 'či', 'článek', 'článku', 'články', 'další', 'dnes', 'do', 'ho', 'i', 'já', 'jak', 'jako', 'je', 'jeho', 'jej', 'její', 'jejich', 'jen', 'jenž', 'ještě', 'ji', 'jiné', 'již', 'jsem', 'jseš', 'jsme', 'jsou', 'jšte', 'k', 'kam', 'každý', 'kde', 'kdo', 'když', 'ke', 'která', 'které', 'kterou', 'který', 'kteři', 'ku', 'ma', 'máte', 'me', 'mě', 'mezi', 'mi', 'mít', 'mně', 'mnou', 'můj', 'může', 'my', 'na', 'ná', 'nad', 'nám', 'napište', 'náš', 'naši', 'ne', 'nebo', 'nechť', 'nejsou', 'není', 'než', 'ní', 'nic', 'nové', 'nový', 'o', 'od', 'ode', 'on', 'pak', 'po', 'pod', 'podle', 'pokud', 'pouze', 'práve', 'pro', 'proč', 'proto', 'protože', 'první', 'před', 'přede', 'přes', 'při', 'pta', 're', 's', 'se', 'si', 'sice', 'strana', 'své', 'svůj', 'svých', 'svým', 'svými', 'ta', 'tak', 'také', 'takže', 'tato', 'te', 'tě', 'tedy', 'těma', 'ten', 'tento', 'této', 'tím', 'tímto', 'tipy', 'to', 'to', 'tohle', 'toho', 'tohoto', 'tom', 'tomto', 'tomuto', 'toto', 'tu', 'tuto', 'tvůj', 'ty', 'tyto', 'u', 'už', 'v', 'vám', 'váš', 'vaše', 've', 'více', 'však', 'všechen', 'vy', 'z', 'za', 'zda', 'zde', 'ze', 'zpět', 'zprávy', 'že'];
				break;
			case 'el' : // Greek
				$stopwords = ['ἃ', 'αἱ', 'αἵ', 'αἳ', 'ἄν', 'ἀλλ\'', 'ἀλλὰ', 'ἄλλος', 'ἅμα', 'ἂν', 'ἀπ', 'ἀπὸ', 'ἄρα', 'αὖ', 'αὐτὸς', 'ἀφ', 'δ\'', 'δι\'', 'δὲ', 'δέ', 'δή', 'δὴ', 'διά', 'διὰ', 'δαὶ', 'δαὶς', 'ἐὰν', 'ἑαυτοῦ', 'ἔτι', 'ἐγὼ', 'ἐκ', 'ἐμὸς', 'ἐν', 'ἐξ', 'επ', 'ἐπὶ', 'εἰ', 'εἴ', 'εἰμὶ', 'εἴμι', 'εἰς', 'εἴτε', 'ἐπεὶ', 'ἐστι', 'ἐφ', 'γάρ', 'γὰρ', 'γε', 'γα^', 'γοῦν', 'ἡ', 'ἢ', 'ἥ', 'ἣ', 'ἧς', 'ἵνα', 'καί', 'καὶ', 'καίτοι', 'κἀν', 'κἂν', 'καθ', 'κατ', 'κατὰ', 'κατά', 'μεθ', 'μἐν', 'μὲν', 'μετ', 'μετὰ', 'μή', 'μὴ', 'μὴν', 'μήτε', 'ὁ', 'ὃ', 'ὅ', 'ὅδε', 'ὅθεν', 'οἷς', 'ὃν', 'ὅπερ', 'ὅς', 'ὃς', 'ὅστις', 'ὅτε', 'ὅτι', 'οὓς', 'οὕτω', 'οὕτως', 'οὗτος', 'οὔτε', 'οὖν', 'οὐδ', 'οὐδεὶς', 'οὐδὲν', 'οἱ', 'οἳ', 'οὗ', 'οὐ', 'οὐδὲ', 'οὐκ', 'οὐχ', 'οὐχὶ', 'παρ', 'παρὰ', 'περὶ', 'ποτε', 'που', 'ποῦ', 'πρὸ', 'προ', 'πρὸς', 'πως', 'σὸς', 'σὺ', 'σὺν', 'τά', 'τὰ', 'ταῖς', 'τὰς', 'τε', 'τὴν', 'τῆς', 'τῇ', 'τι', 'τί', 'τινα', 'τις', 'τίς', 'τὸ', 'τοι', 'τοῖς', 'τοιοῦτος', 'τὸν', 'τότε', 'τοὺς', 'τοῦ', 'τῶν', 'τῷ', 'ὑπ', 'ὑπὲρ', 'ὑπὸ', 'ὡς', 'ὥς', 'ὦ', 'ᾧ', 'ὥστε'];
				break;
			case 'es' : // Spanish
				$stopwords = ['a', 'acuerdo', 'adelante', 'ademas', 'además', 'adrede', 'ahi', 'ahí', 'ahora', 'al', 'alli', 'allí', 'alrededor', 'antano', 'antaño', 'ante', 'antes', 'apenas', 'aproximadamente', 'aquel', 'aquél', 'aquella', 'aquélla', 'aquellas', 'aquéllas', 'aquello', 'aquellos', 'aquéllos', 'aqui', 'aquí', 'arribaabajo', 'asi', 'así', 'aun', 'aún', 'aunque', 'b', 'bajo', 'bastante', 'bien', 'breve', 'c', 'casi', 'cerca', 'claro', 'como', 'cómo', 'con', 'conmigo', 'contigo', 'contra', 'cual', 'cuál', 'cuales', 'cuáles', 'cuando', 'cuándo', 'cuanta', 'cuánta', 'cuantas', 'cuántas', 'cuanto', 'cuánto', 'cuantos', 'cuántos', 'd', 'de', 'debajo', 'del', 'delante', 'demasiado', 'dentro', 'deprisa', 'desde', 'despacio', 'despues', 'después', 'detras', 'detrás', 'dia', 'día', 'dias', 'días', 'donde', 'dónde', 'dos', 'durante', 'e', 'el', 'él', 'ella', 'ellas', 'ellos', 'en', 'encima', 'enfrente', 'enseguida', 'entre', 'es', 'esa', 'ésa', 'esas', 'ésas', 'ese', 'ése', 'eso', 'esos', 'ésos', 'esta', 'está', 'ésta', 'estado', 'estados', 'estan', 'están', 'estar', 'estas', 'éstas', 'este', 'éste', 'esto', 'estos', 'éstos', 'ex', 'excepto', 'f', 'final', 'fue', 'fuera', 'fueron', 'g', 'general', 'gran', 'h', 'ha', 'habia', 'había', 'habla', 'hablan', 'hace', 'hacia', 'han', 'hasta', 'hay', 'horas', 'hoy', 'i', 'incluso', 'informo', 'informó', 'j', 'junto', 'k', 'l', 'la', 'lado', 'las', 'le', 'lejos', 'lo', 'los', 'luego', 'm', 'mal', 'mas', 'más', 'mayor', 'me', 'medio', 'mejor', 'menos', 'menudo', 'mi', 'mí', 'mia', 'mía', 'mias', 'mías', 'mientras', 'mio', 'mío', 'mios', 'míos', 'mis', 'mismo', 'mucho', 'muy', 'n', 'nada', 'nadie', 'ninguna', 'no', 'nos', 'nosotras', 'nosotros', 'nuestra', 'nuestras', 'nuestro', 'nuestros', 'nueva', 'nuevo', 'nunca', 'o', 'os', 'otra', 'otros', 'p', 'pais', 'paìs', 'para', 'parte', 'pasado', 'peor', 'pero', 'poco', 'por', 'porque', 'pronto', 'proximo', 'próximo', 'puede', 'q', 'qeu', 'que', 'qué', 'quien', 'quién', 'quienes', 'quiénes', 'quiza', 'quizá', 'quizas', 'quizás', 'r', 'raras', 'repente', 's', 'salvo', 'se', 'sé', 'segun', 'según', 'ser', 'sera', 'será', 'si', 'sí', 'sido', 'siempre', 'sin', 'sobre', 'solamente', 'solo', 'sólo', 'son', 'soyos', 'su', 'supuesto', 'sus', 'suya', 'suyas', 'suyo', 't', 'tal', 'tambien', 'también', 'tampoco', 'tarde', 'te', 'temprano', 'ti', 'tiene', 'todavia', 'todavía', 'todo', 'todos', 'tras', 'tu', 'tú', 'tus', 'tuya', 'tuyas', 'tuyo', 'tuyos', 'u', 'un', 'una', 'unas', 'uno', 'unos', 'usted', 'ustedes', 'v', 'veces', 'vez', 'vosotras', 'vosotros', 'vuestra', 'vuestras', 'vuestro', 'vuestros', 'w', 'x', 'y', 'ya', 'yo', 'z'];
				break;
			case 'hu' : // Hungarian
				$stopwords = ['a', 'ahogy', 'ahol', 'aki', 'akik', 'akkor', 'alatt', 'által', 'általában', 'amely', 'amelyek', 'amelyekben', 'amelyeket', 'amelyet', 'amelynek', 'ami', 'amit', 'amolyan', 'amíg', 'amikor', 'át', 'abban', 'ahhoz', 'annak', 'arra', 'arról', 'az', 'azok', 'azon', 'azt', 'azzal', 'azért', 'aztán', 'azután', 'azonban', 'bár', 'be', 'belül', 'benne', 'cikk', 'cikkek', 'cikkeket', 'csak', 'de', 'e', 'eddig', 'egész', 'egy', 'egyes', 'egyetlen', 'egyéb', 'egyik', 'egyre', 'ekkor', 'el', 'elég', 'ellen', 'elõ', 'elõször', 'elõtt', 'elsõ', 'én', 'éppen', 'ebben', 'ehhez', 'emilyen', 'ennek', 'erre', 'ez', 'ezt', 'ezek', 'ezen', 'ezzel', 'ezért', 'és', 'fel', 'felé', 'hanem', 'hiszen', 'hogy', 'hogyan', 'igen', 'így', 'illetve', 'ill.', 'ill', 'ilyen', 'ilyenkor', 'ison', 'ismét', 'itt', 'jó', 'jól', 'jobban', 'kell', 'kellett', 'keresztül', 'keressünk', 'ki', 'kívül', 'között', 'közül', 'legalább', 'lehet', 'lehetett', 'legyen', 'lenne', 'lenni', 'lesz', 'lett', 'maga', 'magát', 'majd', 'majd', 'már', 'más', 'másik', 'meg', 'még', 'mellett', 'mert', 'mely', 'melyek', 'mi', 'mit', 'míg', 'miért', 'milyen', 'mikor', 'minden', 'mindent', 'mindenki', 'mindig', 'mint', 'mintha', 'mivel', 'most', 'nagy', 'nagyobb', 'nagyon', 'ne', 'néha', 'nekem', 'neki', 'nem', 'néhány', 'nélkül', 'nincs', 'olyan', 'ott', 'össze', 'õ', 'õk', 'õket', 'pedig', 'persze', 'rá', 's', 'saját', 'sem', 'semmi', 'sok', 'sokat', 'sokkal', 'számára', 'szemben', 'szerint', 'szinte', 'talán', 'tehát', 'teljes', 'tovább', 'továbbá', 'több', 'úgy', 'ugyanis', 'új', 'újabb', 'újra', 'után', 'utána', 'utolsó', 'vagy', 'vagyis', 'valaki', 'valami', 'valamint', 'való', 'vagyok', 'van', 'vannak', 'volt', 'voltam', 'voltak', 'voltunk', 'vissza', 'vele', 'viszont', 'volna'];
				break;
			case 'pl' : // Polish
				$stopwords = ['a', 'aby', 'ach', 'acz', 'aczkolwiek', 'aj', 'albo', 'ale', 'ależ', 'aż', 'bardziej', 'bardzo', 'bez', 'bo', 'bowiem', 'by', 'byli', 'bynajmniej', 'być', 'był', 'była', 'było', 'były', 'będzie', 'będą', 'cali', 'cała', 'cały', 'ci', 'cię', 'ciebie', 'co', 'cokolwiek', 'coś', 'czasami', 'czasem', 'czemu', 'czy', 'czyli', 'daleko', 'dla', 'dlaczego', 'dlatego', 'do', 'dobrze', 'dokąd', 'dość', 'dużo', 'dwa', 'dwaj', 'dwie', 'dwoje', 'dziś', 'dzisiaj', 'gdy', 'gdyby', 'gdyż', 'gdzie', 'gdziekolwiek', 'gdzieś', 'go', 'i', 'ich', 'ile', 'im', 'inna', 'inne', 'inny', 'innych', 'iż', 'ja', 'ją', 'jak', 'jakaś', 'jakby', 'jaki', 'jakichś', 'jakie', 'jakiś', 'jakiż', 'jakkolwiek', 'jako', 'jakoś', 'je', 'jeden', 'jedna', 'jedno', 'jednak', 'jednakże', 'jego', 'jej', 'jemu', 'jest', 'jestem', 'jeszcze', 'jeśli', 'jeżeli', 'już', 'ją', 'każdy', 'kiedy', 'kilka', 'kimś', 'kto', 'ktokolwiek', 'ktoś', 'która', 'które', 'którego', 'której', 'który', 'których', 'którym', 'którzy', 'ku', 'lat', 'lecz', 'lub', 'ma', 'mają', 'mam', 'mi', 'mimo', 'między', 'mną', 'mnie', 'mogą', 'moi', 'moim', 'moja', 'moje', 'może', 'możliwe', 'można', 'mój', 'mu', 'musi', 'my', 'na', 'nad', 'nam', 'nami', 'nas', 'nasi', 'nasz', 'nasza', 'nasze', 'naszego', 'naszych', 'natomiast', 'natychmiast', 'nawet', 'nią', 'nic', 'nich', 'nie', 'niego', 'niej', 'niemu', 'nigdy', 'nim', 'nimi', 'niż', 'no', 'o', 'obok', 'od', 'około', 'on', 'ona', 'one', 'oni', 'ono', 'oraz', 'owszem', 'pan', 'pana', 'pani', 'po', 'pod', 'podczas', 'pomimo', 'ponad', 'ponieważ', 'powinien', 'powinna', 'powinni', 'powinno', 'poza', 'prawie', 'przecież', 'przed', 'przede', 'przedtem', 'przez', 'przy', 'roku', 'również', 'sam', 'sama', 'są', 'się', 'skąd', 'sobie', 'sobą', 'sposób', 'swoje', 'są', 'ta', 'tak', 'taka', 'taki', 'takie', 'także', 'tam', 'te', 'tego', 'tej', 'ten', 'teraz', 'też', 'totobą', 'tobie', 'toteż', 'trzeba', 'tu', 'tutaj', 'twoi', 'twoim', 'twoja', 'twoje', 'twym', 'twój', 'ty', 'tych', 'tylko', 'tym', 'u', 'w', 'wam', 'wami', 'was', 'wasz', 'wasza', 'wasze', 'we', 'według', 'wiele', 'wielu', 'więc', 'więcej', 'wszyscy', 'wszystkich', 'wszystkie', 'wszystkim', 'wszystko', 'wtedy', 'wy', 'właśnie', 'z', 'za', 'zapewne', 'zawsze', 'zeznowu', 'znów', 'został', 'żaden', 'żadna', 'żadne', 'żadnych', 'że', 'żeby'];
				break;
			case 'pt' : // Portuguese
				$stopwords = ['acerca', 'agora', 'algmas', 'alguns', 'ali', 'ambos', 'antes', 'apontar', 'aquela', 'aquelas', 'aquele', 'aqueles', 'aqui', 'atrás', 'bem', 'bom', 'cada', 'caminho', 'cima', 'com', 'como', 'comprido', 'conhecido', 'corrente', 'das', 'debaixo', 'dentro', 'desde', 'desligado', 'deve', 'devem', 'deverá', 'direita', 'diz', 'dizer', 'dois', 'dos', 'e', 'é', 'ela', 'ele', 'eles', 'em', 'enquanto', 'então', 'está', 'estado', 'estão', 'estar', 'estará', 'este', 'estes', 'esteve', 'estive', 'estivemos', 'estiveram', 'eu', 'fará', 'faz', 'fazer', 'fazia', 'fez', 'fim', 'foi', 'fora', 'horas', 'iniciar', 'inicio', 'ir', 'irá', 'ista', 'iste', 'isto', 'ligado', 'maioria', 'maiorias', 'mais', 'mas', 'mesmo', 'meu', 'muito', 'muitos', 'não', 'nome', 'nós', 'nosso', 'novo', 'o', 'onde', 'os', 'ou', 'outro', 'para', 'parte', 'pegar', 'pelo', 'pessoas', 'pode', 'poderá', 'podia', 'por', 'porque', 'povo', 'promeiro', 'qual', 'qualquer', 'quando', 'quê', 'quem', 'quieto', 'saber', 'são', 'sem', 'ser', 'seu', 'somente', 'tal', 'também', 'tem', 'têm', 'tempo', 'tenho', 'tentar', 'tentaram', 'tente', 'tentei', 'teu', 'teve', 'tipo', 'tive', 'todos', 'trabalhar', 'trabalho', 'tu', 'último', 'um', 'uma', 'umas', 'uns', 'usa', 'usar', 'valor', 'veja', 'ver', 'verdade', 'verdadeiro', 'você'];
				break;
			case 'sk' : // Slovak
				$stopwords = ['a', 'aby', 'aj', 'ak', 'aká', 'akáže', 'aké', 'akého', 'akéhože', 'akej', 'akejže', 'akému', 'akémuže', 'akéže', 'ako', 'akom', 'akomže', 'akou', 'akouže', 'akože', 'akú', 'akúže', 'aký', 'akých', 'akýchže', 'akým', 'akými', 'akýmiže', 'akýmže', 'akýže', 'ale', 'alebo', 'ani', 'áno', 'asi', 'avšak', 'až', 'ba', 'bez', 'bezo', 'bol', 'bola', 'boli', 'bolo', 'buď', 'bude', 'budem', 'budeme', 'budeš', 'budete', 'budú', 'by', 'byť', 'cez', 'cezo', 'čej', 'či', 'čí', 'čia', 'čie', 'čieho', 'čiemu', 'čím', 'čími', 'čiu', 'čo', 'čoho', 'čom', 'čomu', 'čou', 'čože', 'ďalší', 'ďalšia', 'ďalšie', 'ďalšieho', 'ďalšiemu', 'ďalších', 'ďalším', 'ďalšími', 'ďalšiu', 'ďalšom', 'ďalšou', 'dnes', 'do', 'ešte', 'ho', 'hoci', 'i', 'iba', 'ich', 'im', 'iná', 'iné', 'iného', 'inej', 'inému', 'iní', 'inom', 'inú', 'iný', 'iných', 'iným', 'inými', 'ja', 'je', 'jeho', 'jej', 'jemu', 'ju', 'k', 'ká', 'kam', 'kamže', 'každá', 'každé', 'každého', 'každému', 'každí', 'každou', 'každú', 'každý', 'každých', 'každým', 'každými', 'káže', 'kde', 'ké', 'keď', 'keďže', 'kej', 'kejže', 'kéže', 'kie', 'kieho', 'kiehože', 'kiemu', 'kiemuže', 'kieže', 'koho', 'kom', 'komu', 'kou', 'kouže', 'kto', 'ktorá', 'ktoré', 'ktorej', 'ktorí', 'ktorou', 'ktorú', 'ktorý', 'ktorých', 'ktorým', 'ktorými', 'ku', 'kú', 'kúže', 'ký', 'kýho', 'kýhože', 'kým', 'kýmu', 'kýmuže', 'kýže', 'lebo', 'leda', 'ledaže', 'len', 'ma', 'má', 'majú', 'mal', 'mala', 'mali', 'mám', 'máme', 'máš', 'mať', 'máte', 'medzi', 'mi', 'mňa', 'mne', 'mnou', 'moja', 'moje', 'mojej', 'mojich', 'mojim', 'mojimi', 'mojou', 'moju', 'možno', 'môcť', 'môj', 'môjho', 'môže', 'môžem', 'môžeme', 'môžeš', 'môžete', 'môžu', 'mu', 'musí', 'musia', 'musieť', 'musím', 'musíme', 'musíš', 'musíte', 'my', 'na', 'nad', 'nado', 'najmä', 'nám', 'nami', 'nás', 'náš', 'naša', 'naše', 'našej', 'nášho', 'naši', 'našich', 'našim', 'našimi', 'našou', 'ne', 'neho', 'nech', 'nej', 'nejaká', 'nejaké', 'nejakého', 'nejakej', 'nejakému', 'nejakom', 'nejakou', 'nejakú', 'nejaký', 'nejakých', 'nejakým', 'nejakými', 'nemu', 'než', 'nič', 'ničím', 'ničoho', 'ničom', 'ničomu', 'nie', 'niečo', 'niektorá', 'niektoré', 'niektorého', 'niektorej', 'niektorému', 'niektorom', 'niektorou', 'niektorú', 'niektorý', 'niektorých', 'niektorým', 'niektorými', 'nielen', 'nich', 'nim', 'ním', 'nimi', 'no', 'ňom', 'ňou', 'ňu', 'o', 'od', 'odo', 'on', 'oň', 'ona', 'oňho', 'oni', 'ono', 'ony', 'po', 'pod', 'podľa', 'podo', 'pokiaľ', 'popod', 'popri', 'potom', 'poza', 'práve', 'pre', 'prečo', 'pred', 'predo', 'preto', 'pretože', 'pri', 's', 'sa', 'seba', 'sebe', 'sebou', 'sem', 'si', 'sme', 'so', 'som', 'ste', 'sú', 'svoj', 'svoja', 'svoje', 'svojho', 'svojich', 'svojim', 'svojím', 'svojimi', 'svojou', 'svoju', 'ta', 'tá', 'tak', 'taká', 'takáto', 'také', 'takéto', 'takej', 'takejto', 'takého', 'takéhoto', 'takému', 'takémuto', 'takí', 'taký', 'takýto', 'takú', 'takúto', 'takže', 'tam', 'táto', 'teba', 'tebe', 'tebou', 'teda', 'tej', 'tejto', 'ten', 'tento', 'ti', 'tí', 'tie', 'tieto', 'tiež', 'títo', 'to', 'toho', 'tohto', 'tohoto', 'tom', 'tomto', 'tomu', 'tomuto', 'toto', 'tou', 'touto', 'tu', 'tú', 'túto', 'tvoj', 'tvoja', 'tvoje', 'tvojej', 'tvojho', 'tvoji', 'tvojich', 'tvojim', 'tvojím', 'tvojimi', 'ty', 'tých', 'tým', 'tými', 'týmto', 'u', 'už', 'v', 'vám', 'vami', 'vás', 'váš', 'vaša', 'vaše', 'vašej', 'vášho', 'vaši', 'vašich', 'vašim', 'vaším', 'veď', 'viac', 'vo', 'však', 'všetci', 'všetka', 'všetko', 'všetky', 'všetok', 'vy', 'z', 'za', 'začo', 'začože', 'zo', 'že'];
				break;
		}
	}
	// return the stopwords (empty array if we do not have the language)
    return $stopwords;
}