<?php 
/**
 * Contains and initializes the Parser class.
 * @package w3Checker
 */
/**
 * 
 */
require_once('class.Parser.HTML5Lib.php');
require_once('class.Utils.php');
/**
 * Parser class
 * 
 * @todo review
 * @package w3Checker
 * @author Thomas Gambet <tgambet@w3.org>
 * @copyright 2011 W3C ® (MIT, ERCIM, Keio)
 * @license http://www.w3.org/Consortium/Legal/copyright-software
 */
abstract class Parser {
	
	private static $logger;
	
	public $markup;
	public $contentType;
	public $mimetype;
	public $charset;
	public $isHTML = false;
	public $isHTML5 = false;
	public $isXHTML10 = false;
	public $isXHTML11 = false;
	// = isXHTML10 || isXHTML11 
	public $isXHTML1x = false;
	// = isHTML5 && isServedAsXML
	public $isXHTML5 = false;
	// = isXHTML10 || isXHTML11 || isXHTML5
	public $isXML = false;
	public $isRDFa = false;
	public $isServedAsXML = false;
	// DOMDocument object
	public $document;
	// Store of cached results of certain functions
	private $cache;
	public $dirControls;
	public $i18nAttributes;
	public $i18nElements;
	public $langTags;
	public $numEscapes;
	public $encLabels = array(
		"UTF-8"=>"UTF-8", "UNICODE-1-1-UTF-8"=>"UTF-8", "UTF8"=>"UTF-8", 
		"IBM866"=>"IBM866", "866"=>"IBM866", "CP866"=>"IBM866", "CSIBM866"=>"IBM866", 
		"ISO-8859-1"=>"WINDOWS-1252", 
		"ISO-8859-2"=>"ISO-8859-2", "CSISOLATIN2"=>"ISO-8859-2", "ISO-IR-101"=>"ISO-8859-2", "ISO8859-2"=>"ISO-8859-2", "ISO88592"=>"ISO-8859-2", "ISO_8859-2"=>"ISO-8859-2", "ISO_8859-2:1987"=>"ISO-8859-2", "L2"=>"ISO-8859-2", "LATIN2"=>"ISO-8859-2", 
		"ISO-8859-3"=>"ISO-8859-3", "CSISOLATIN3"=>"ISO-8859-3", "ISO-IR-109"=>"ISO-8859-3", "ISO8859-3"=>"ISO-8859-3", "ISO88593"=>"ISO-8859-3", "ISO_8859-3"=>"ISO-8859-3", "ISO_8859-3:1988"=>"ISO-8859-3", "L3"=>"ISO-8859-3", "LATIN3"=>"ISO-8859-3", 
		"ISO-8859-4"=>"ISO-8859-4", "CSISOLATIN4"=>"ISO-8859-4", "ISO-IR-110"=>"ISO-8859-4", "ISO8859-4"=>"ISO-8859-4", "ISO88594"=>"ISO-8859-4", "ISO_8859-4"=>"ISO-8859-4", "ISO_8859-4:1988"=>"ISO-8859-4", "L4"=>"ISO-8859-4", "LATIN4"=>"ISO-8859-4", 
		"ISO-8859-5"=>"ISO-8859-5", "CSISOLATINCYRILLIC"=>"ISO-8859-5", "CYRILLIC"=>"ISO-8859-5", "ISO-IR-144"=>"ISO-8859-5", "ISO8859-5"=>"ISO-8859-5", "ISO88595"=>"ISO-8859-5", "ISO_8859-5"=>"ISO-8859-5", "ISO_8859-5:1988"=>"ISO-8859-5", 
		"ISO-8859-6"=>"ISO-8859-6", "ARABIC"=>"ISO-8859-6", "ASMO-708"=>"ISO-8859-6", "CSISO88596E"=>"ISO-8859-6", "CSISO88596I"=>"ISO-8859-6", "CSISOLATINARABIC"=>"ISO-8859-6", "ECMA-114"=>"ISO-8859-6", "ISO-8859-6-E"=>"ISO-8859-6", "ISO-8859-6-I"=>"ISO-8859-6", "ISO-IR-127"=>"ISO-8859-6", "ISO8859-6"=>"ISO-8859-6", "ISO88596"=>"ISO-8859-6", "ISO_8859-6"=>"ISO-8859-6", "ISO_8859-6:1987"=>"ISO-8859-6",
		"ISO-8859-7"=>"ISO-8859-7", "CSISOLATINGREEK"=>"ISO-8859-7", "ECMA-118"=>"ISO-8859-7", "ELOT_928"=>"ISO-8859-7", "GREEK"=>"ISO-8859-7", "GREEK8"=>"ISO-8859-7", "ISO-IR-126"=>"ISO-8859-7", "SUN_EU_GREEK"=>"ISO-8859-7", "ISO8859-7"=>"ISO-8859-7", "ISO88597"=>"ISO-8859-7", "ISO_8859-7"=>"ISO-8859-7", "ISO_8859-7:1987"=>"ISO-8859-7", 
		"ISO-8859-8"=>"ISO-8859-8", "CSISO88598E"=>"ISO-8859-8", "CSISOLATINHEBREW"=>"ISO-8859-8", "HEBREW"=>"ISO-8859-8", "ISO-8859-8-E"=>"ISO-8859-8", "ISO-IR-138"=>"ISO-8859-8", "ISO8859-8"=>"ISO-8859-8", "ISO88598"=>"ISO-8859-8", "ISO_8859-8"=>"ISO-8859-8", "ISO_8859-8:1988"=>"ISO-8859-8", "VISUAL"=>"ISO-8859-8", 
		"ISO-8859-8-I"=>"ISO-8859-8-I", "CSISO88598I"=>"ISO-8859-8-1", "LOGICAL"=>"ISO-8859-8-I", 
		"ISO-8859-10"=>"ISO-8859-10", "CSISOLATIN6"=>"ISO-8859-10", "ISO-IR-157"=>"ISO-8859-10", "ISO8859-10"=>"ISO-8859-10", "ISO885910"=>"ISO-8859-10", "L6"=>"ISO-8859-10", "LATIN6"=>"ISO-8859-10", 
		"ISO-8859-13"=>"ISO-8859-13", "ISO8859-13"=>"ISO-8859-13", "ISO885913"=>"ISO-8859-13", 
		"ISO-8859-14"=>"ISO-8859-14", "ISO8859-14"=>"ISO-8859-14", "ISO885914"=>"ISO-8859-14", 
		"ISO-8859-15"=>"ISO-8859-15", "CSISOLATIN9"=>"ISO-8859-15", "ISO8859-15"=>"ISO-8859-15", "ISO885915"=>"ISO-8859-15", "ISO_8859-15"=>"ISO-8859-15", "L9"=>"ISO-8859-15", 
		"ISO-8859-16"=>"ISO-8859-16", 
		"KOI8-R"=>"KOI8-R", "CSKOI8R"=>"KOI8-R", "KOI"=>"KOI8-R", "KOI8"=>"KOI8-R", "KOI8_R"=>"KOI8-R", 
		"KOI8-U"=>"KOI8-U", "KOI8-RU"=>"KOI8-U", 
		"MACINTOSH"=>"MACINTOSH", "CSMACINTOSH"=>"MACINTOSH", "MAC"=>"MACINTOSH", "X-MAC-ROMAN"=>"MACINTOSH", 
		"WINDOWS-874"=>"WINDOWS-874", "DOS-874"=>"WINDOWS-874", "ISO-8859-11"=>"WINDOWS-874", "ISO8859-11"=>"WINDOWS-874", "ISO885911"=>"WINDOWS-874", "TIS-620"=>"WINDOWS-874", 
		"WINDOWS-1250"=>"WINDOWS-1250", "CP1250"=>"WINDOWS-1250", "X-CP1250"=>"WINDOWS-1250", 
		"WINDOWS-1251"=>"WINDOWS-1251", "CP1251"=>"WINDOWS-1251", "X-CP1251"=>"WINDOWS-1251", 
		"WINDOWS-1252"=>"WINDOWS-1252", "ANSI_X3.4-1968"=>"WINDOWS-1252", "ASCII"=>"WINDOWS-1252", "CP1252"=>"WINDOWS-1252", "CP819"=>"WINDOWS-1252", "CSISOLATIN1"=>"WINDOWS-1252", "IBM819"=>"WINDOWS-1252", "ISO-8859-1"=>"WINDOWS-1252", "ISO-IR-100"=>"WINDOWS-1252", "ISO8859-1"=>"WINDOWS-1252", "ISO88591"=>"WINDOWS-1252", "ISO_8859-1"=>"WINDOWS-1252", "ISO_8859-1:1987"=>"WINDOWS-1252", "L1"=>"WINDOWS-1252", "LATIN1"=>"WINDOWS-1252", "US-ASCII"=>"WINDOWS-1252", "X-CP1252"=>"WINDOWS-1252", 
		"WINDOWS-1253"=>"WINDOWS-1253", "CP1253"=>"WINDOWS-1253", "X-CP1253"=>"WINDOWS-1253", 
		"WINDOWS-1254"=>"WINDOWS-1254", "CP1254"=>"WINDOWS-1254", "X-CP1254"=>"WINDOWS-1254", "CSISOLATIN5"=>"WINDOWS-1254", "ISO-8859-9"=>"WINDOWS-1254", "ISO-IR-148"=>"WINDOWS-1254", "ISO8859-9"=>"WINDOWS-1254", "ISO88599"=>"WINDOWS-1254", "ISO_8859-9"=>"WINDOWS-1254", "ISO_8859-9:1989"=>"WINDOWS-1254", "L5"=>"WINDOWS-1254", "LATIN5"=>"WINDOWS-1254", 
		"WINDOWS-1255"=>"WINDOWS-1255", "CP1255"=>"WINDOWS-1255", "X-CP1255"=>"WINDOWS-1255", 
		"WINDOWS-1256"=>"WINDOWS-1256", "CP1256"=>"WINDOWS-1256", "X-CP1256"=>"WINDOWS-1256", 
		"WINDOWS-1257"=>"WINDOWS-1257", "CP1257"=>"WINDOWS-1257", "X-CP1257"=>"WINDOWS-1257", 
		"WINDOWS-1258"=>"WINDOWS-1258", "CP1258"=>"WINDOWS-1258", "X-CP1258"=>"WINDOWS-1258", 
		"X-MAC-CYRILLIC"=>"X-MAC-CYRILLIC", "X-MAC-UKRAINIAN"=>"X-MAC-CYRILLIC", 
		"GBK"=>"GBK", "CHINESE"=>"GBK", "CSGB2312"=>"GBK", "CSISO58GB231280"=>"GBK", "GB2312"=>"GBK", "GB_2312"=>"GBK", "GB_2312-80"=>"GBK", "ISO-IR-58"=>"GBK", "X-GBK"=>"GBK", "GB18030"=>"GB18030", 
		"BIG5"=>"BIG5", "BIG5-HKSCS"=>"BIG5", "CN-BIG5"=>"BIG5", "CSBIG5"=>"BIG5", "X-X-BIG5"=>"BIG5", 
		"EUC-JP"=>"EUC-JP", "CSEUCPKDFMTJAPANESE"=>"EUC-JP", "X-EUC-JP"=>"EUC-JP", 
		"ISO-2022-JP"=>"ISO-2022-JP", "CSISO2022JP"=>"ISO-2022-JP", 
		"SHIFT_JIS"=>"SHIFT_JIS", "CSSHIFTJIS"=>"SHIFT_JIS", "MS932"=>"SHIFT_JIS", "MS_KANJI"=>"SHIFT_JIS", "SHIFT-JIS"=>"SHIFT_JIS", "SJIS"=>"SHIFT_JIS", "WINDOWS-31J"=>"SHIFT_JIS", "X-SJIS"=>"SHIFT_JIS", 
		"EUC-KR"=>"EUC-KR", "CSEUCKR"=>"EUC-KR", "CSKSC56011987"=>"EUC-KR", "ISO-IR-149"=>"EUC-KR", "KOREAN"=>"EUC-KR", "KS_C_5601-1987"=>"EUC-KR", "KS_C_5601-1989"=>"EUC-KR", "KSC5601"=>"EUC-KR", "KSC_5601"=>"EUC-KR", "WINDOWS-949"=>"EUC-KR", 
		"UTF-16BE"=>"UTF-16BE", 
		"UTF-16LE"=>"UTF-16LE","UTF-16"=>"UTF-16", 
		);




	public $languages = "|aa|ab|ae|af|ak|am|an|ar|as|av|ay|az|ba|be|bg|bh|bi|bm|bn|bo|br|bs|ca|ce|ch|co|cr|cs|cu|cv|cy|da|de|dv|dz|ee|el|en|eo|es|et|eu|fa|ff|fi|fj|fo|fr|fy|ga|gd|gl|gn|gu|gv|ha|he|hi|ho|hr|ht|hu|hy|hz|ia|id|ie|ig|ii|ik|in|io|is|it|iu|iw|ja|ji|jv|jw|ka|kg|ki|kj|kk|kl|km|kn|ko|kr|ks|ku|kv|kw|ky|la|lb|lg|li|ln|lo|lt|lu|lv|mg|mh|mi|mk|ml|mn|mo|mr|ms|mt|my|na|nb|nd|ne|ng|nl|nn|no|nr|nv|ny|oc|oj|om|or|os|pa|pi|pl|ps|pt|qu|rm|rn|ro|ru|rw|sa|sc|sd|se|sg|sh|si|sk|sl|sm|sn|so|sq|sr|ss|st|su|sv|sw|ta|te|tg|th|ti|tk|tl|tn|to|tr|ts|tt|tw|ty|ug|uk|ur|uz|ve|vi|vo|wa|wo|xh|yi|yo|za|zh|zu|aaa|aab|aac|aad|aae|aaf|aag|aah|aai|aak|aal|aam|aan|aao|aap|aaq|aas|aat|aau|aav|aaw|aax|aaz|aba|abb|abc|abd|abe|abf|abg|abh|abi|abj|abl|abm|abn|abo|abp|abq|abr|abs|abt|abu|abv|abw|abx|aby|abz|aca|acb|acd|ace|acf|ach|aci|ack|acl|acm|acn|acp|acq|acr|acs|act|acu|acv|acw|acx|acy|acz|ada|adb|add|ade|adf|adg|adh|adi|adj|adl|adn|ado|adp|adq|adr|ads|adt|adu|adw|adx|ady|adz|aea|aeb|aec|aed|aee|aek|ael|aem|aen|aeq|aer|aes|aeu|aew|aey|aez|afa|afb|afd|afe|afg|afh|afi|afk|afn|afo|afp|afs|aft|afu|afz|aga|agb|agc|agd|age|agf|agg|agh|agi|agj|agk|agl|agm|agn|ago|agp|agq|agr|ags|agt|agu|agv|agw|agx|agy|agz|aha|ahb|ahg|ahh|ahi|ahk|ahl|ahm|ahn|aho|ahp|ahr|ahs|aht|aia|aib|aic|aid|aie|aif|aig|aih|aii|aij|aik|ail|aim|ain|aio|aip|aiq|air|ais|ait|aiw|aix|aiy|aja|ajg|aji|ajn|ajp|ajt|aju|ajw|ajz|akb|akc|akd|ake|akf|akg|akh|aki|akj|akk|akl|akm|ako|akp|akq|akr|aks|akt|aku|akv|akw|akx|aky|akz|ala|alc|ald|ale|alf|alg|alh|ali|alj|alk|all|alm|aln|alo|alp|alq|alr|als|alt|alu|alv|alw|alx|aly|alz|ama|amb|amc|ame|amf|amg|ami|amj|amk|aml|amm|amn|amo|amp|amq|amr|ams|amt|amu|amv|amw|amx|amy|amz|ana|anb|anc|and|ane|anf|ang|anh|ani|anj|ank|anl|anm|ann|ano|anp|anq|anr|ans|ant|anu|anv|anw|anx|any|anz|aoa|aob|aoc|aod|aoe|aof|aog|aoh|aoi|aoj|aok|aol|aom|aon|aor|aos|aot|aou|aox|aoz|apa|apb|apc|apd|ape|apf|apg|aph|api|apj|apk|apl|apm|apn|apo|app|apq|apr|aps|apt|apu|apv|apw|apx|apy|apz|aqa|aqc|aqd|aqg|aql|aqm|aqn|aqp|aqr|aqt|aqz|arb|arc|ard|are|arh|ari|arj|ark|arl|arn|aro|arp|arq|arr|ars|art|aru|arv|arw|arx|ary|arz|asa|asb|asc|asd|ase|asf|asg|ash|asi|asj|ask|asl|asn|aso|asp|asq|asr|ass|ast|asu|asv|asw|asx|asy|asz|ata|atb|atc|atd|ate|atg|ath|ati|atj|atk|atl|atm|atn|ato|atp|atq|atr|ats|att|atu|atv|atw|atx|aty|atz|aua|aub|auc|aud|aue|auf|aug|auh|aui|auj|auk|aul|aum|aun|auo|aup|auq|aur|aus|aut|auu|auw|aux|auy|auz|avb|avd|avi|avk|avl|avm|avn|avo|avs|avt|avu|avv|awa|awb|awc|awd|awe|awg|awh|awi|awk|awm|awn|awo|awr|aws|awt|awu|awv|aww|awx|awy|axb|axe|axg|axk|axl|axm|axx|aya|ayb|ayc|ayd|aye|ayg|ayh|ayi|ayk|ayl|ayn|ayo|ayp|ayq|ayr|ays|ayt|ayu|ayx|ayy|ayz|aza|azb|azc|azd|azg|azj|azm|azn|azo|azt|azz|baa|bab|bac|bad|bae|baf|bag|bah|bai|baj|bal|ban|bao|bap|bar|bas|bat|bau|bav|baw|bax|bay|baz|bba|bbb|bbc|bbd|bbe|bbf|bbg|bbh|bbi|bbj|bbk|bbl|bbm|bbn|bbo|bbp|bbq|bbr|bbs|bbt|bbu|bbv|bbw|bbx|bby|bbz|bca|bcb|bcc|bcd|bce|bcf|bcg|bch|bci|bcj|bck|bcl|bcm|bcn|bco|bcp|bcq|bcr|bcs|bct|bcu|bcv|bcw|bcy|bcz|bda|bdb|bdc|bdd|bde|bdf|bdg|bdh|bdi|bdj|bdk|bdl|bdm|bdn|bdo|bdp|bdq|bdr|bds|bdt|bdu|bdv|bdw|bdx|bdy|bdz|bea|beb|bec|bed|bee|bef|beg|beh|bei|bej|bek|bem|beo|bep|beq|ber|bes|bet|beu|bev|bew|bex|bey|bez|bfa|bfb|bfc|bfd|bfe|bff|bfg|bfh|bfi|bfj|bfk|bfl|bfm|bfn|bfo|bfp|bfq|bfr|bfs|bft|bfu|bfw|bfx|bfy|bfz|bga|bgb|bgc|bgd|bge|bgf|bgg|bgi|bgj|bgk|bgl|bgm|bgn|bgo|bgp|bgq|bgr|bgs|bgt|bgu|bgv|bgw|bgx|bgy|bgz|bha|bhb|bhc|bhd|bhe|bhf|bhg|bhh|bhi|bhj|bhk|bhl|bhm|bhn|bho|bhp|bhq|bhr|bhs|bht|bhu|bhv|bhw|bhx|bhy|bhz|bia|bib|bic|bid|bie|bif|big|bij|bik|bil|bim|bin|bio|bip|biq|bir|bit|biu|biv|biw|bix|biy|biz|bja|bjb|bjc|bjd|bje|bjf|bjg|bjh|bji|bjj|bjk|bjl|bjm|bjn|bjo|bjp|bjq|bjr|bjs|bjt|bju|bjv|bjw|bjx|bjy|bjz|bka|bkb|bkc|bkd|bkf|bkg|bkh|bki|bkj|bkk|bkl|bkm|bkn|bko|bkp|bkq|bkr|bks|bkt|bku|bkv|bkw|bkx|bky|bkz|bla|blb|blc|bld|ble|blf|blg|blh|bli|blj|blk|bll|blm|bln|blo|blp|blq|blr|bls|blt|blv|blw|blx|bly|blz|bma|bmb|bmc|bmd|bme|bmf|bmg|bmh|bmi|bmj|bmk|bml|bmm|bmn|bmo|bmp|bmq|bmr|bms|bmt|bmu|bmv|bmw|bmx|bmy|bmz|bna|bnb|bnc|bnd|bne|bnf|bng|bni|bnj|bnk|bnl|bnm|bnn|bno|bnp|bnq|bnr|bns|bnt|bnu|bnv|bnw|bnx|bny|bnz|boa|bob|boe|bof|bog|boh|boi|boj|bok|bol|bom|bon|boo|bop|boq|bor|bot|bou|bov|bow|box|boy|boz|bpa|bpb|bpd|bpg|bph|bpi|bpj|bpk|bpl|bpm|bpn|bpo|bpp|bpq|bpr|bps|bpt|bpu|bpv|bpw|bpx|bpy|bpz|bqa|bqb|bqc|bqd|bqf|bqg|bqh|bqi|bqj|bqk|bql|bqm|bqn|bqo|bqp|bqq|bqr|bqs|bqt|bqu|bqv|bqw|bqx|bqy|bqz|bra|brb|brc|brd|brf|brg|brh|bri|brj|brk|brl|brm|brn|bro|brp|brq|brr|brs|brt|bru|brv|brw|brx|bry|brz|bsa|bsb|bsc|bse|bsf|bsg|bsh|bsi|bsj|bsk|bsl|bsm|bsn|bso|bsp|bsq|bsr|bss|bst|bsu|bsv|bsw|bsx|bsy|bta|btb|btc|btd|bte|btf|btg|bth|bti|btj|btk|btl|btm|btn|bto|btp|btq|btr|bts|btt|btu|btv|btw|btx|bty|btz|bua|bub|buc|bud|bue|buf|bug|buh|bui|buj|buk|bum|bun|buo|bup|buq|bus|but|buu|buv|buw|bux|buy|buz|bva|bvb|bvc|bvd|bve|bvf|bvg|bvh|bvi|bvj|bvk|bvl|bvm|bvn|bvo|bvp|bvq|bvr|bvt|bvu|bvv|bvw|bvx|bvy|bvz|bwa|bwb|bwc|bwd|bwe|bwf|bwg|bwh|bwi|bwj|bwk|bwl|bwm|bwn|bwo|bwp|bwq|bwr|bws|bwt|bwu|bww|bwx|bwy|bwz|bxa|bxb|bxc|bxd|bxe|bxf|bxg|bxh|bxi|bxj|bxk|bxl|bxm|bxn|bxo|bxp|bxq|bxr|bxs|bxu|bxv|bxw|bxx|bxz|bya|byb|byc|byd|bye|byf|byg|byh|byi|byj|byk|byl|bym|byn|byo|byp|byq|byr|bys|byt|byv|byw|byx|byy|byz|bza|bzb|bzc|bzd|bze|bzf|bzg|bzh|bzi|bzj|bzk|bzl|bzm|bzn|bzo|bzp|bzq|bzr|bzs|bzt|bzu|bzv|bzw|bzx|bzy|bzz|caa|cab|cac|cad|cae|caf|cag|cah|cai|caj|cak|cal|cam|can|cao|cap|caq|car|cas|cau|cav|caw|cax|cay|caz|cba|cbb|cbc|cbd|cbe|cbg|cbh|cbi|cbj|cbk|cbl|cbn|cbo|cbq|cbr|cbs|cbt|cbu|cbv|cbw|cby|cca|ccc|ccd|cce|ccg|cch|ccj|ccl|ccm|ccn|cco|ccp|ccq|ccr|ccs|cda|cdc|cdd|cde|cdf|cdg|cdh|cdi|cdj|cdm|cdn|cdo|cdr|cds|cdy|cdz|cea|ceb|ceg|cek|cel|cen|cet|cfa|cfd|cfg|cfm|cga|cgc|cgg|cgk|chb|chc|chd|chf|chg|chh|chj|chk|chl|chm|chn|cho|chp|chq|chr|cht|chw|chx|chy|chz|cia|cib|cic|cid|cie|cih|cik|cim|cin|cip|cir|ciw|ciy|cja|cje|cjh|cji|cjk|cjm|cjn|cjo|cjp|cjr|cjs|cjv|cjy|cka|ckb|ckh|ckl|ckn|cko|ckq|ckr|cks|ckt|cku|ckv|ckx|cky|ckz|cla|clc|cld|cle|clh|cli|clj|clk|cll|clm|clo|clt|clu|clw|cly|cma|cmc|cme|cmg|cmi|cmk|cml|cmm|cmn|cmo|cmr|cms|cmt|cna|cnb|cnc|cng|cnh|cni|cnk|cnl|cno|cns|cnt|cnu|cnw|cnx|coa|cob|coc|cod|coe|cof|cog|coh|coj|cok|col|com|con|coo|cop|coq|cot|cou|cov|cow|cox|coy|coz|cpa|cpb|cpc|cpe|cpf|cpg|cpi|cpn|cpo|cpp|cps|cpu|cpx|cpy|cqd|cqu|cra|crb|crc|crd|crf|crg|crh|cri|crj|crk|crl|crm|crn|cro|crp|crq|crr|crs|crt|crv|crw|crx|cry|crz|csa|csb|csc|csd|cse|csf|csg|csh|csi|csj|csk|csl|csm|csn|cso|csq|csr|css|cst|csu|csv|csw|csy|csz|cta|ctc|ctd|cte|ctg|cth|ctl|ctm|ctn|cto|ctp|cts|ctt|ctu|ctz|cua|cub|cuc|cug|cuh|cui|cuj|cuk|cul|cum|cuo|cup|cuq|cur|cus|cut|cuu|cuv|cuw|cux|cvg|cvn|cwa|cwb|cwd|cwe|cwg|cwt|cya|cyb|cyo|czh|czk|czn|czo|czt|daa|dac|dad|dae|daf|dag|dah|dai|daj|dak|dal|dam|dao|dap|daq|dar|das|dau|dav|daw|dax|day|daz|dba|dbb|dbd|dbe|dbf|dbg|dbi|dbj|dbl|dbm|dbn|dbo|dbp|dbq|dbr|dbt|dbu|dbv|dbw|dby|dcc|dcr|dda|ddd|dde|ddg|ddi|ddj|ddn|ddo|ddr|dds|ddw|dec|ded|dee|def|deg|deh|dei|dek|del|dem|den|dep|deq|der|des|dev|dez|dga|dgb|dgc|dgd|dge|dgg|dgh|dgi|dgk|dgl|dgn|dgo|dgr|dgs|dgt|dgu|dgw|dgx|dgz|dha|dhd|dhg|dhi|dhl|dhm|dhn|dho|dhr|dhs|dhu|dhv|dhw|dhx|dia|dib|dic|did|dif|dig|dih|dii|dij|dik|dil|dim|din|dio|dip|diq|dir|dis|dit|diu|diw|dix|diy|diz|dja|djb|djc|djd|dje|djf|dji|djj|djk|djl|djm|djn|djo|djr|dju|djw|dka|dkk|dkl|dkr|dks|dkx|dlg|dlk|dlm|dln|dma|dmb|dmc|dmd|dme|dmg|dmk|dml|dmm|dmn|dmo|dmr|dms|dmu|dmv|dmw|dmx|dmy|dna|dnd|dne|dng|dni|dnj|dnk|dnn|dnr|dnt|dnu|dnv|dnw|dny|doa|dob|doc|doe|dof|doh|doi|dok|dol|don|doo|dop|doq|dor|dos|dot|dov|dow|dox|doy|doz|dpp|dra|drb|drc|drd|dre|drg|drh|dri|drl|drn|dro|drq|drr|drs|drt|dru|drw|dry|dsb|dse|dsh|dsi|dsl|dsn|dso|dsq|dta|dtb|dtd|dth|dti|dtk|dtm|dtn|dto|dtp|dtr|dts|dtt|dtu|dty|dua|dub|duc|dud|due|duf|dug|duh|dui|duj|duk|dul|dum|dun|duo|dup|duq|dur|dus|duu|duv|duw|dux|duy|duz|dva|dwa|dwl|dwr|dws|dwu|dww|dwy|dya|dyb|dyd|dyg|dyi|dym|dyn|dyo|dyu|dyy|dza|dzd|dze|dzg|dzl|dzn|eaa|ebg|ebk|ebo|ebr|ebu|ecr|ecs|ecy|eee|efa|efe|efi|ega|egl|ego|egx|egy|ehu|eip|eit|eiv|eja|eka|ekc|eke|ekg|eki|ekk|ekl|ekm|eko|ekp|ekr|eky|ele|elh|eli|elk|elm|elo|elp|elu|elx|ema|emb|eme|emg|emi|emk|emm|emn|emo|emp|ems|emu|emw|emx|emy|ena|enb|enc|end|enf|enh|enl|enm|enn|eno|enq|enr|enu|env|enw|enx|eot|epi|era|erg|erh|eri|erk|ero|err|ers|ert|erw|ese|esg|esh|esi|esk|esl|esm|esn|eso|esq|ess|esu|esx|esy|etb|etc|eth|etn|eto|etr|ets|ett|etu|etx|etz|euq|eve|evh|evn|ewo|ext|eya|eyo|eza|eze|faa|fab|fad|faf|fag|fah|fai|faj|fak|fal|fam|fan|fap|far|fat|fau|fax|fay|faz|fbl|fcs|fer|ffi|ffm|fgr|fia|fie|fil|fip|fir|fit|fiu|fiw|fkk|fkv|fla|flh|fli|fll|fln|flr|fly|fmp|fmu|fnb|fng|fni|fod|foi|fom|fon|for|fos|fox|fpe|fqs|frc|frd|frk|frm|fro|frp|frq|frr|frs|frt|fse|fsl|fss|fub|fuc|fud|fue|fuf|fuh|fui|fuj|fum|fun|fuq|fur|fut|fuu|fuv|fuy|fvr|fwa|fwe|gaa|gab|gac|gad|gae|gaf|gag|gah|gai|gaj|gak|gal|gam|gan|gao|gap|gaq|gar|gas|gat|gau|gav|gaw|gax|gay|gaz|gba|gbb|gbc|gbd|gbe|gbf|gbg|gbh|gbi|gbj|gbk|gbl|gbm|gbn|gbo|gbp|gbq|gbr|gbs|gbu|gbv|gbw|gbx|gby|gbz|gcc|gcd|gce|gcf|gcl|gcn|gcr|gct|gda|gdb|gdc|gdd|gde|gdf|gdg|gdh|gdi|gdj|gdk|gdl|gdm|gdn|gdo|gdq|gdr|gds|gdt|gdu|gdx|gea|geb|gec|ged|geg|geh|gei|gej|gek|gel|gem|geq|ges|gev|gew|gex|gey|gez|gfk|gft|gfx|gga|ggb|ggd|gge|ggg|ggk|ggl|ggn|ggo|ggr|ggt|ggu|ggw|gha|ghc|ghe|ghh|ghk|ghl|ghn|gho|ghr|ghs|ght|gia|gib|gic|gid|gig|gih|gil|gim|gin|gio|gip|giq|gir|gis|git|giu|giw|gix|giy|giz|gji|gjk|gjm|gjn|gjr|gju|gka|gke|gkn|gko|gkp|gku|glc|gld|glh|gli|glj|glk|gll|glo|glr|glu|glw|gly|gma|gmb|gmd|gme|gmg|gmh|gml|gmm|gmn|gmq|gmu|gmv|gmw|gmx|gmy|gmz|gna|gnb|gnc|gnd|gne|gng|gnh|gni|gnk|gnl|gnm|gnn|gno|gnq|gnr|gnt|gnu|gnw|gnz|goa|gob|goc|god|goe|gof|gog|goh|goi|goj|gok|gol|gom|gon|goo|gop|goq|gor|gos|got|gou|gow|gox|goy|goz|gpa|gpe|gpn|gqa|gqi|gqn|gqr|gqu|gra|grb|grc|grd|grg|grh|gri|grj|grk|grm|gro|grq|grr|grs|grt|gru|grv|grw|grx|gry|grz|gse|gsg|gsl|gsm|gsn|gso|gsp|gss|gsw|gta|gti|gtu|gua|gub|guc|gud|gue|guf|gug|guh|gui|guk|gul|gum|gun|guo|gup|guq|gur|gus|gut|guu|guv|guw|gux|guz|gva|gvc|gve|gvf|gvj|gvl|gvm|gvn|gvo|gvp|gvr|gvs|gvy|gwa|gwb|gwc|gwd|gwe|gwf|gwg|gwi|gwj|gwm|gwn|gwr|gwt|gwu|gww|gwx|gxx|gya|gyb|gyd|gye|gyf|gyg|gyi|gyl|gym|gyn|gyr|gyy|gza|gzi|gzn|haa|hab|hac|had|hae|haf|hag|hah|hai|haj|hak|hal|ham|han|hao|hap|haq|har|has|hav|haw|hax|hay|haz|hba|hbb|hbn|hbo|hbu|hca|hch|hdn|hds|hdy|hea|hed|heg|heh|hei|hem|hgm|hgw|hhi|hhr|hhy|hia|hib|hid|hif|hig|hih|hii|hij|hik|hil|him|hio|hir|hit|hiw|hix|hji|hka|hke|hkk|hks|hla|hlb|hld|hle|hlt|hlu|hma|hmb|hmc|hmd|hme|hmf|hmg|hmh|hmi|hmj|hmk|hml|hmm|hmn|hmp|hmq|hmr|hms|hmt|hmu|hmv|hmw|hmx|hmy|hmz|hna|hnd|hne|hnh|hni|hnj|hnn|hno|hns|hnu|hoa|hob|hoc|hod|hoe|hoh|hoi|hoj|hok|hol|hom|hoo|hop|hor|hos|hot|hov|how|hoy|hoz|hpo|hps|hra|hrc|hre|hrk|hrm|hro|hrp|hrr|hrt|hru|hrw|hrx|hrz|hsb|hsh|hsl|hsn|hss|hti|hto|hts|htu|htx|hub|huc|hud|hue|huf|hug|huh|hui|huj|huk|hul|hum|huo|hup|huq|hur|hus|hut|huu|huv|huw|hux|huy|huz|hvc|hve|hvk|hvn|hvv|hwa|hwc|hwo|hya|hyx|iai|ian|iap|iar|iba|ibb|ibd|ibe|ibg|ibi|ibl|ibm|ibn|ibr|ibu|iby|ica|ich|icl|icr|ida|idb|idc|idd|ide|idi|idr|ids|idt|idu|ifa|ifb|ife|iff|ifk|ifm|ifu|ify|igb|ige|igg|igl|igm|ign|igo|igs|igw|ihb|ihi|ihp|ihw|iin|iir|ijc|ije|ijj|ijn|ijo|ijs|ike|iki|ikk|ikl|iko|ikp|ikr|iks|ikt|ikv|ikw|ikx|ikz|ila|ilb|ilg|ili|ilk|ill|ilm|ilo|ilp|ils|ilu|ilv|ilw|ima|ime|imi|iml|imn|imo|imr|ims|imy|inb|inc|ine|ing|inh|inj|inl|inm|inn|ino|inp|ins|int|inz|ior|iou|iow|ipi|ipo|iqu|iqw|ira|ire|irh|iri|irk|irn|iro|irr|iru|irx|iry|isa|isc|isd|ise|isg|ish|isi|isk|ism|isn|iso|isr|ist|isu|itb|itc|itd|ite|iti|itk|itl|itm|ito|itr|its|itt|itv|itw|itx|ity|itz|ium|ivb|ivv|iwk|iwm|iwo|iws|ixc|ixl|iya|iyo|iyx|izh|izi|izr|izz|jaa|jab|jac|jad|jae|jaf|jah|jaj|jak|jal|jam|jan|jao|jaq|jar|jas|jat|jau|jax|jay|jaz|jbe|jbi|jbj|jbk|jbn|jbo|jbr|jbt|jbu|jbw|jcs|jct|jda|jdg|jdt|jeb|jee|jeg|jeh|jei|jek|jel|jen|jer|jet|jeu|jgb|jge|jgk|jgo|jhi|jhs|jia|jib|jic|jid|jie|jig|jih|jii|jil|jim|jio|jiq|jit|jiu|jiv|jiy|jje|jjr|jka|jkm|jko|jkp|jkr|jku|jle|jls|jma|jmb|jmc|jmd|jmi|jml|jmn|jmr|jms|jmw|jmx|jna|jnd|jng|jni|jnj|jnl|jns|job|jod|jog|jor|jos|jow|jpa|jpr|jpx|jqr|jra|jrb|jrr|jrt|jru|jsl|jua|jub|juc|jud|juh|jui|juk|jul|jum|jun|juo|jup|jur|jus|jut|juu|juw|juy|jvd|jvn|jwi|jya|jye|jyy|kaa|kab|kac|kad|kae|kaf|kag|kah|kai|kaj|kak|kam|kao|kap|kaq|kar|kav|kaw|kax|kay|kba|kbb|kbc|kbd|kbe|kbf|kbg|kbh|kbi|kbj|kbk|kbl|kbm|kbn|kbo|kbp|kbq|kbr|kbs|kbt|kbu|kbv|kbw|kbx|kby|kbz|kca|kcb|kcc|kcd|kce|kcf|kcg|kch|kci|kcj|kck|kcl|kcm|kcn|kco|kcp|kcq|kcr|kcs|kct|kcu|kcv|kcw|kcx|kcy|kcz|kda|kdc|kdd|kde|kdf|kdg|kdh|kdi|kdj|kdk|kdl|kdm|kdn|kdo|kdp|kdq|kdr|kdt|kdu|kdv|kdw|kdx|kdy|kdz|kea|keb|kec|ked|kee|kef|keg|keh|kei|kej|kek|kel|kem|ken|keo|kep|keq|ker|kes|ket|keu|kev|kew|kex|key|kez|kfa|kfb|kfc|kfd|kfe|kff|kfg|kfh|kfi|kfj|kfk|kfl|kfm|kfn|kfo|kfp|kfq|kfr|kfs|kft|kfu|kfv|kfw|kfx|kfy|kfz|kga|kgb|kgc|kgd|kge|kgf|kgg|kgh|kgi|kgj|kgk|kgl|kgm|kgn|kgo|kgp|kgq|kgr|kgs|kgt|kgu|kgv|kgw|kgx|kgy|kha|khb|khc|khd|khe|khf|khg|khh|khi|khj|khk|khl|khn|kho|khp|khq|khr|khs|kht|khu|khv|khw|khx|khy|khz|kia|kib|kic|kid|kie|kif|kig|kih|kii|kij|kil|kim|kio|kip|kiq|kis|kit|kiu|kiv|kiw|kix|kiy|kiz|kja|kjb|kjc|kjd|kje|kjf|kjg|kjh|kji|kjj|kjk|kjl|kjm|kjn|kjo|kjp|kjq|kjr|kjs|kjt|kju|kjv|kjx|kjy|kjz|kka|kkb|kkc|kkd|kke|kkf|kkg|kkh|kki|kkj|kkk|kkl|kkm|kkn|kko|kkp|kkq|kkr|kks|kkt|kku|kkv|kkw|kkx|kky|kkz|kla|klb|klc|kld|kle|klf|klg|klh|kli|klj|klk|kll|klm|kln|klo|klp|klq|klr|kls|klt|klu|klv|klw|klx|kly|klz|kma|kmb|kmc|kmd|kme|kmf|kmg|kmh|kmi|kmj|kmk|kml|kmm|kmn|kmo|kmp|kmq|kmr|kms|kmt|kmu|kmv|kmw|kmx|kmy|kmz|kna|knb|knc|knd|kne|knf|kng|kni|knj|knk|knl|knm|knn|kno|knp|knq|knr|kns|knt|knu|knv|knw|knx|kny|knz|koa|koc|kod|koe|kof|kog|koh|koi|koj|kok|kol|koo|kop|koq|kos|kot|kou|kov|kow|kox|koy|koz|kpa|kpb|kpc|kpd|kpe|kpf|kpg|kph|kpi|kpj|kpk|kpl|kpm|kpn|kpo|kpp|kpq|kpr|kps|kpt|kpu|kpv|kpw|kpx|kpy|kpz|kqa|kqb|kqc|kqd|kqe|kqf|kqg|kqh|kqi|kqj|kqk|kql|kqm|kqn|kqo|kqp|kqq|kqr|kqs|kqt|kqu|kqv|kqw|kqx|kqy|kqz|kra|krb|krc|krd|kre|krf|krh|kri|krj|krk|krl|krm|krn|kro|krp|krr|krs|krt|kru|krv|krw|krx|kry|krz|ksa|ksb|ksc|ksd|kse|ksf|ksg|ksh|ksi|ksj|ksk|ksl|ksm|ksn|kso|ksp|ksq|ksr|kss|kst|ksu|ksv|ksw|ksx|ksy|ksz|kta|ktb|ktc|ktd|kte|ktf|ktg|kth|kti|ktj|ktk|ktl|ktm|ktn|kto|ktp|ktq|ktr|kts|ktt|ktu|ktv|ktw|ktx|kty|ktz|kub|kuc|kud|kue|kuf|kug|kuh|kui|kuj|kuk|kul|kum|kun|kuo|kup|kuq|kus|kut|kuu|kuv|kuw|kux|kuy|kuz|kva|kvb|kvc|kvd|kve|kvf|kvg|kvh|kvi|kvj|kvk|kvl|kvm|kvn|kvo|kvp|kvq|kvr|kvs|kvt|kvu|kvv|kvw|kvx|kvy|kvz|kwa|kwb|kwc|kwd|kwe|kwf|kwg|kwh|kwi|kwj|kwk|kwl|kwm|kwn|kwo|kwp|kwq|kwr|kws|kwt|kwu|kwv|kww|kwx|kwy|kwz|kxa|kxb|kxc|kxd|kxe|kxf|kxh|kxi|kxj|kxk|kxl|kxm|kxn|kxo|kxp|kxq|kxr|kxs|kxt|kxu|kxv|kxw|kxx|kxy|kxz|kya|kyb|kyc|kyd|kye|kyf|kyg|kyh|kyi|kyj|kyk|kyl|kym|kyn|kyo|kyp|kyq|kyr|kys|kyt|kyu|kyv|kyw|kyx|kyy|kyz|kza|kzb|kzc|kzd|kze|kzf|kzg|kzh|kzi|kzj|kzk|kzl|kzm|kzn|kzo|kzp|kzq|kzr|kzs|kzt|kzu|kzv|kzw|kzx|kzy|kzz|laa|lab|lac|lad|lae|laf|lag|lah|lai|laj|lak|lal|lam|lan|lap|laq|lar|las|lau|law|lax|lay|laz|lba|lbb|lbc|lbe|lbf|lbg|lbi|lbj|lbk|lbl|lbm|lbn|lbo|lbq|lbr|lbs|lbt|lbu|lbv|lbw|lbx|lby|lbz|lcc|lcd|lce|lcf|lch|lcl|lcm|lcp|lcq|lcs|lda|ldb|ldd|ldg|ldh|ldi|ldj|ldk|ldl|ldm|ldn|ldo|ldp|ldq|lea|leb|lec|led|lee|lef|leg|leh|lei|lej|lek|lel|lem|len|leo|lep|leq|ler|les|let|leu|lev|lew|lex|ley|lez|lfa|lfn|lga|lgb|lgg|lgh|lgi|lgk|lgl|lgm|lgn|lgq|lgr|lgt|lgu|lgz|lha|lhh|lhi|lhl|lhm|lhn|lhp|lhs|lht|lhu|lia|lib|lic|lid|lie|lif|lig|lih|lii|lij|lik|lil|lio|lip|liq|lir|lis|liu|liv|liw|lix|liy|liz|lja|lje|lji|ljl|ljp|ljw|ljx|lka|lkb|lkc|lkd|lke|lkh|lki|lkj|lkl|lkm|lkn|lko|lkr|lks|lkt|lku|lky|lla|llb|llc|lld|lle|llf|llg|llh|lli|llj|llk|lll|llm|lln|llo|llp|llq|lls|llu|llx|lma|lmb|lmc|lmd|lme|lmf|lmg|lmh|lmi|lmj|lmk|lml|lmm|lmn|lmo|lmp|lmq|lmr|lmu|lmv|lmw|lmx|lmy|lmz|lna|lnb|lnd|lng|lnh|lni|lnj|lnl|lnm|lnn|lno|lns|lnu|lnw|lnz|loa|lob|loc|loe|lof|log|loh|loi|loj|lok|lol|lom|lon|loo|lop|loq|lor|los|lot|lou|lov|low|lox|loy|loz|lpa|lpe|lpn|lpo|lpx|lra|lrc|lre|lrg|lri|lrk|lrl|lrm|lrn|lro|lrr|lrt|lrv|lrz|lsa|lsd|lse|lsg|lsh|lsi|lsl|lsm|lso|lsp|lsr|lss|lst|lsy|ltc|ltg|lti|ltn|lto|lts|ltu|lua|luc|lud|lue|luf|lui|luj|luk|lul|lum|lun|luo|lup|luq|lur|lus|lut|luu|luv|luw|luy|luz|lva|lvk|lvs|lvu|lwa|lwe|lwg|lwh|lwl|lwm|lwo|lwt|lwu|lww|lya|lyg|lyn|lzh|lzl|lzn|lzz|maa|mab|mad|mae|maf|mag|mai|maj|mak|mam|man|map|maq|mas|mat|mau|mav|maw|max|maz|mba|mbb|mbc|mbd|mbe|mbf|mbh|mbi|mbj|mbk|mbl|mbm|mbn|mbo|mbp|mbq|mbr|mbs|mbt|mbu|mbv|mbw|mbx|mby|mbz|mca|mcb|mcc|mcd|mce|mcf|mcg|mch|mci|mcj|mck|mcl|mcm|mcn|mco|mcp|mcq|mcr|mcs|mct|mcu|mcv|mcw|mcx|mcy|mcz|mda|mdb|mdc|mdd|mde|mdf|mdg|mdh|mdi|mdj|mdk|mdl|mdm|mdn|mdp|mdq|mdr|mds|mdt|mdu|mdv|mdw|mdx|mdy|mdz|mea|meb|mec|med|mee|mef|meg|meh|mei|mej|mek|mel|mem|men|meo|mep|meq|mer|mes|met|meu|mev|mew|mey|mez|mfa|mfb|mfc|mfd|mfe|mff|mfg|mfh|mfi|mfj|mfk|mfl|mfm|mfn|mfo|mfp|mfq|mfr|mfs|mft|mfu|mfv|mfw|mfx|mfy|mfz|mga|mgb|mgc|mgd|mge|mgf|mgg|mgh|mgi|mgj|mgk|mgl|mgm|mgn|mgo|mgp|mgq|mgr|mgs|mgt|mgu|mgv|mgw|mgx|mgy|mgz|mha|mhb|mhc|mhd|mhe|mhf|mhg|mhh|mhi|mhj|mhk|mhl|mhm|mhn|mho|mhp|mhq|mhr|mhs|mht|mhu|mhw|mhx|mhy|mhz|mia|mib|mic|mid|mie|mif|mig|mih|mii|mij|mik|mil|mim|min|mio|mip|miq|mir|mis|mit|miu|miw|mix|miy|miz|mja|mjb|mjc|mjd|mje|mjg|mjh|mji|mjj|mjk|mjl|mjm|mjn|mjo|mjp|mjq|mjr|mjs|mjt|mju|mjv|mjw|mjx|mjy|mjz|mka|mkb|mkc|mke|mkf|mkg|mkh|mki|mkj|mkk|mkl|mkm|mkn|mko|mkp|mkq|mkr|mks|mkt|mku|mkv|mkw|mkx|mky|mkz|mla|mlb|mlc|mld|mle|mlf|mlh|mli|mlj|mlk|mll|mlm|mln|mlo|mlp|mlq|mlr|mls|mlu|mlv|mlw|mlx|mlz|mma|mmb|mmc|mmd|mme|mmf|mmg|mmh|mmi|mmj|mmk|mml|mmm|mmn|mmo|mmp|mmq|mmr|mmt|mmu|mmv|mmw|mmx|mmy|mmz|mna|mnb|mnc|mnd|mne|mnf|mng|mnh|mni|mnj|mnk|mnl|mnm|mnn|mno|mnp|mnq|mnr|mns|mnt|mnu|mnv|mnw|mnx|mny|mnz|moa|moc|mod|moe|mof|mog|moh|moi|moj|mok|mom|moo|mop|moq|mor|mos|mot|mou|mov|mow|mox|moy|moz|mpa|mpb|mpc|mpd|mpe|mpg|mph|mpi|mpj|mpk|mpl|mpm|mpn|mpo|mpp|mpq|mpr|mps|mpt|mpu|mpv|mpw|mpx|mpy|mpz|mqa|mqb|mqc|mqe|mqf|mqg|mqh|mqi|mqj|mqk|mql|mqm|mqn|mqo|mqp|mqq|mqr|mqs|mqt|mqu|mqv|mqw|mqx|mqy|mqz|mra|mrb|mrc|mrd|mre|mrf|mrg|mrh|mrj|mrk|mrl|mrm|mrn|mro|mrp|mrq|mrr|mrs|mrt|mru|mrv|mrw|mrx|mry|mrz|msb|msc|msd|mse|msf|msg|msh|msi|msj|msk|msl|msm|msn|mso|msp|msq|msr|mss|mst|msu|msv|msw|msx|msy|msz|mta|mtb|mtc|mtd|mte|mtf|mtg|mth|mti|mtj|mtk|mtl|mtm|mtn|mto|mtp|mtq|mtr|mts|mtt|mtu|mtv|mtw|mtx|mty|mua|mub|muc|mud|mue|mug|muh|mui|muj|muk|mul|mum|mun|muo|mup|muq|mur|mus|mut|muu|muv|mux|muy|muz|mva|mvb|mvd|mve|mvf|mvg|mvh|mvi|mvk|mvl|mvm|mvn|mvo|mvp|mvq|mvr|mvs|mvt|mvu|mvv|mvw|mvx|mvy|mvz|mwa|mwb|mwc|mwd|mwe|mwf|mwg|mwh|mwi|mwj|mwk|mwl|mwm|mwn|mwo|mwp|mwq|mwr|mws|mwt|mwu|mwv|mww|mwx|mwy|mwz|mxa|mxb|mxc|mxd|mxe|mxf|mxg|mxh|mxi|mxj|mxk|mxl|mxm|mxn|mxo|mxp|mxq|mxr|mxs|mxt|mxu|mxv|mxw|mxx|mxy|mxz|myb|myc|myd|mye|myf|myg|myh|myi|myj|myk|myl|mym|myn|myo|myp|myq|myr|mys|myt|myu|myv|myw|myx|myy|myz|mza|mzb|mzc|mzd|mze|mzg|mzh|mzi|mzj|mzk|mzl|mzm|mzn|mzo|mzp|mzq|mzr|mzs|mzt|mzu|mzv|mzw|mzx|mzy|mzz|naa|nab|nac|nad|nae|naf|nag|nah|nai|naj|nak|nal|nam|nan|nao|nap|naq|nar|nas|nat|naw|nax|nay|naz|nba|nbb|nbc|nbd|nbe|nbf|nbg|nbh|nbi|nbj|nbk|nbm|nbn|nbo|nbp|nbq|nbr|nbs|nbt|nbu|nbv|nbw|nbx|nby|nca|ncb|ncc|ncd|nce|ncf|ncg|nch|nci|ncj|nck|ncl|ncm|ncn|nco|ncp|ncr|ncs|nct|ncu|ncx|ncz|nda|ndb|ndc|ndd|ndf|ndg|ndh|ndi|ndj|ndk|ndl|ndm|ndn|ndp|ndq|ndr|nds|ndt|ndu|ndv|ndw|ndx|ndy|ndz|nea|neb|nec|ned|nee|nef|neg|neh|nei|nej|nek|nem|nen|neo|neq|ner|nes|net|neu|nev|new|nex|ney|nez|nfa|nfd|nfl|nfr|nfu|nga|ngb|ngc|ngd|nge|ngf|ngg|ngh|ngi|ngj|ngk|ngl|ngm|ngn|ngo|ngp|ngq|ngr|ngs|ngt|ngu|ngv|ngw|ngx|ngy|ngz|nha|nhb|nhc|nhd|nhe|nhf|nhg|nhh|nhi|nhk|nhm|nhn|nho|nhp|nhq|nhr|nht|nhu|nhv|nhw|nhx|nhy|nhz|nia|nib|nic|nid|nie|nif|nig|nih|nii|nij|nik|nil|nim|nin|nio|niq|nir|nis|nit|niu|niv|niw|nix|niy|niz|nja|njb|njd|njh|nji|njj|njl|njm|njn|njo|njr|njs|njt|nju|njx|njy|njz|nka|nkb|nkc|nkd|nke|nkf|nkg|nkh|nki|nkj|nkk|nkm|nkn|nko|nkp|nkq|nkr|nks|nkt|nku|nkv|nkw|nkx|nkz|nla|nlc|nle|nlg|nli|nlj|nlk|nll|nln|nlo|nlq|nlr|nlu|nlv|nlw|nlx|nly|nlz|nma|nmb|nmc|nmd|nme|nmf|nmg|nmh|nmi|nmj|nmk|nml|nmm|nmn|nmo|nmp|nmq|nmr|nms|nmt|nmu|nmv|nmw|nmx|nmy|nmz|nna|nnb|nnc|nnd|nne|nnf|nng|nnh|nni|nnj|nnk|nnl|nnm|nnn|nnp|nnq|nnr|nns|nnt|nnu|nnv|nnw|nnx|nny|nnz|noa|noc|nod|noe|nof|nog|noh|noi|noj|nok|nol|nom|non|noo|nop|noq|nos|not|nou|nov|now|noy|noz|npa|npb|npg|nph|npi|npl|npn|npo|nps|npu|npy|nqg|nqk|nqm|nqn|nqo|nqq|nqy|nra|nrb|nrc|nre|nrf|nrg|nri|nrk|nrl|nrm|nrn|nrp|nrr|nrt|nru|nrx|nrz|nsa|nsc|nsd|nse|nsf|nsg|nsh|nsi|nsk|nsl|nsm|nsn|nso|nsp|nsq|nsr|nss|nst|nsu|nsv|nsw|nsx|nsy|nsz|ntd|nte|ntg|nti|ntj|ntk|ntm|nto|ntp|ntr|nts|ntu|ntw|ntx|nty|ntz|nua|nub|nuc|nud|nue|nuf|nug|nuh|nui|nuj|nuk|nul|num|nun|nuo|nup|nuq|nur|nus|nut|nuu|nuv|nuw|nux|nuy|nuz|nvh|nvm|nvo|nwa|nwb|nwc|nwe|nwg|nwi|nwm|nwo|nwr|nwx|nwy|nxa|nxd|nxe|nxg|nxi|nxk|nxl|nxm|nxn|nxo|nxq|nxr|nxu|nxx|nyb|nyc|nyd|nye|nyf|nyg|nyh|nyi|nyj|nyk|nyl|nym|nyn|nyo|nyp|nyq|nyr|nys|nyt|nyu|nyv|nyw|nyx|nyy|nza|nzb|nzi|nzk|nzm|nzs|nzu|nzy|nzz|oaa|oac|oar|oav|obi|obk|obl|obm|obo|obr|obt|obu|oca|och|oco|ocu|oda|odk|odt|odu|ofo|ofs|ofu|ogb|ogc|oge|ogg|ogo|ogu|oht|ohu|oia|oin|ojb|ojc|ojg|ojp|ojs|ojv|ojw|oka|okb|okd|oke|okg|okh|oki|okj|okk|okl|okm|okn|oko|okr|oks|oku|okv|okx|ola|old|ole|olk|olm|olo|olr|olt|olu|oma|omb|omc|ome|omg|omi|omk|oml|omn|omo|omp|omq|omr|omt|omu|omv|omw|omx|ona|onb|one|ong|oni|onj|onk|onn|ono|onp|onr|ons|ont|onu|onw|onx|ood|oog|oon|oor|oos|opa|opk|opm|opo|opt|opy|ora|orc|ore|org|orh|orn|oro|orr|ors|ort|oru|orv|orw|orx|ory|orz|osa|osc|osi|oso|osp|ost|osu|osx|ota|otb|otd|ote|oti|otk|otl|otm|otn|oto|otq|otr|ots|ott|otu|otw|otx|oty|otz|oua|oub|oue|oui|oum|oun|ovd|owi|owl|oyb|oyd|oym|oyy|ozm|paa|pab|pac|pad|pae|paf|pag|pah|pai|pak|pal|pam|pao|pap|paq|par|pas|pat|pau|pav|paw|pax|pay|paz|pbb|pbc|pbe|pbf|pbg|pbh|pbi|pbl|pbn|pbo|pbp|pbr|pbs|pbt|pbu|pbv|pby|pbz|pca|pcb|pcc|pcd|pce|pcf|pcg|pch|pci|pcj|pck|pcl|pcm|pcn|pcp|pcr|pcw|pda|pdc|pdi|pdn|pdo|pdt|pdu|pea|peb|ped|pee|pef|peg|peh|pei|pej|pek|pel|pem|peo|pep|peq|pes|pev|pex|pey|pez|pfa|pfe|pfl|pga|pgd|pgg|pgi|pgk|pgl|pgn|pgs|pgu|pgy|pgz|pha|phd|phg|phh|phi|phk|phl|phm|phn|pho|phq|phr|pht|phu|phv|phw|pia|pib|pic|pid|pie|pif|pig|pih|pii|pij|pil|pim|pin|pio|pip|pir|pis|pit|piu|piv|piw|pix|piy|piz|pjt|pka|pkb|pkc|pkg|pkh|pkn|pko|pkp|pkr|pks|pkt|pku|pla|plb|plc|pld|ple|plf|plg|plh|plj|plk|pll|pln|plo|plp|plq|plr|pls|plt|plu|plv|plw|ply|plz|pma|pmb|pmc|pmd|pme|pmf|pmh|pmi|pmj|pmk|pml|pmm|pmn|pmo|pmq|pmr|pms|pmt|pmu|pmw|pmx|pmy|pmz|pna|pnb|pnc|pne|png|pnh|pni|pnj|pnk|pnl|pnm|pnn|pno|pnp|pnq|pnr|pns|pnt|pnu|pnv|pnw|pnx|pny|pnz|poc|pod|poe|pof|pog|poh|poi|pok|pom|pon|poo|pop|poq|pos|pot|pov|pow|pox|poy|poz|ppa|ppe|ppi|ppk|ppl|ppm|ppn|ppo|ppp|ppq|ppr|pps|ppt|ppu|pqa|pqe|pqm|pqw|pra|prb|prc|prd|pre|prf|prg|prh|pri|prk|prl|prm|prn|pro|prp|prq|prr|prs|prt|pru|prw|prx|pry|prz|psa|psc|psd|pse|psg|psh|psi|psl|psm|psn|pso|psp|psq|psr|pss|pst|psu|psw|psy|pta|pth|pti|ptn|pto|ptp|ptq|ptr|ptt|ptu|ptv|ptw|pty|pua|pub|puc|pud|pue|puf|pug|pui|puj|puk|pum|puo|pup|puq|pur|put|puu|puw|pux|puy|puz|pwa|pwb|pwg|pwi|pwm|pwn|pwo|pwr|pww|pxm|pye|pym|pyn|pys|pyu|pyx|pyy|pzn|qaa..qtz|qua|qub|quc|qud|quf|qug|quh|qui|quk|qul|qum|qun|qup|quq|qur|qus|quv|quw|qux|quy|quz|qva|qvc|qve|qvh|qvi|qvj|qvl|qvm|qvn|qvo|qvp|qvs|qvw|qvy|qvz|qwa|qwc|qwe|qwh|qwm|qws|qwt|qxa|qxc|qxh|qxl|qxn|qxo|qxp|qxq|qxr|qxs|qxt|qxu|qxw|qya|qyp|raa|rab|rac|rad|raf|rag|rah|rai|raj|rak|ral|ram|ran|rao|rap|raq|rar|ras|rat|rau|rav|raw|rax|ray|raz|rbb|rbk|rbl|rbp|rcf|rdb|rea|reb|ree|reg|rei|rej|rel|rem|ren|rer|res|ret|rey|rga|rge|rgk|rgn|rgr|rgs|rgu|rhg|rhp|ria|rie|rif|ril|rim|rin|rir|rit|riu|rjg|rji|rjs|rka|rkb|rkh|rki|rkm|rkt|rkw|rma|rmb|rmc|rmd|rme|rmf|rmg|rmh|rmi|rmk|rml|rmm|rmn|rmo|rmp|rmq|rmr|rms|rmt|rmu|rmv|rmw|rmx|rmy|rmz|rna|rnd|rng|rnl|rnn|rnp|rnr|rnw|roa|rob|roc|rod|roe|rof|rog|rol|rom|roo|rop|ror|rou|row|rpn|rpt|rri|rro|rrt|rsb|rsi|rsl|rsm|rtc|rth|rtm|rts|rtw|rub|ruc|rue|ruf|rug|ruh|rui|ruk|ruo|rup|ruq|rut|ruu|ruy|ruz|rwa|rwk|rwm|rwo|rwr|rxd|rxw|ryn|rys|ryu|rzh|saa|sab|sac|sad|sae|saf|sah|sai|saj|sak|sal|sam|sao|sap|saq|sar|sas|sat|sau|sav|saw|sax|say|saz|sba|sbb|sbc|sbd|sbe|sbf|sbg|sbh|sbi|sbj|sbk|sbl|sbm|sbn|sbo|sbp|sbq|sbr|sbs|sbt|sbu|sbv|sbw|sbx|sby|sbz|sca|scb|sce|scf|scg|sch|sci|sck|scl|scn|sco|scp|scq|scs|scu|scv|scw|scx|sda|sdb|sdc|sde|sdf|sdg|sdh|sdj|sdk|sdl|sdm|sdn|sdo|sdp|sdr|sds|sdt|sdu|sdv|sdx|sdz|sea|seb|sec|sed|see|sef|seg|seh|sei|sej|sek|sel|sem|sen|seo|sep|seq|ser|ses|set|seu|sev|sew|sey|sez|sfb|sfe|sfm|sfs|sfw|sga|sgb|sgc|sgd|sge|sgg|sgh|sgi|sgj|sgk|sgl|sgm|sgn|sgo|sgp|sgr|sgs|sgt|sgu|sgw|sgx|sgy|sgz|sha|shb|shc|shd|she|shg|shh|shi|shj|shk|shl|shm|shn|sho|shp|shq|shr|shs|sht|shu|shv|shw|shx|shy|shz|sia|sib|sid|sie|sif|sig|sih|sii|sij|sik|sil|sim|sio|sip|siq|sir|sis|sit|siu|siv|siw|six|siy|siz|sja|sjb|sjd|sje|sjg|sjk|sjl|sjm|sjn|sjo|sjp|sjr|sjs|sjt|sju|sjw|ska|skb|skc|skd|ske|skf|skg|skh|ski|skj|skk|skm|skn|sko|skp|skq|skr|sks|skt|sku|skv|skw|skx|sky|skz|sla|slc|sld|sle|slf|slg|slh|sli|slj|sll|slm|sln|slp|slq|slr|sls|slt|slu|slw|slx|sly|slz|sma|smb|smc|smd|smf|smg|smh|smi|smj|smk|sml|smm|smn|smp|smq|smr|sms|smt|smu|smv|smw|smx|smy|smz|snb|snc|sne|snf|sng|snh|sni|snj|snk|snl|snm|snn|sno|snp|snq|snr|sns|snu|snv|snw|snx|sny|snz|soa|sob|soc|sod|soe|sog|soh|soi|soj|sok|sol|son|soo|sop|soq|sor|sos|sou|sov|sow|sox|soy|soz|spb|spc|spd|spe|spg|spi|spk|spl|spm|spn|spo|spp|spq|spr|sps|spt|spu|spv|spx|spy|sqa|sqh|sqj|sqk|sqm|sqn|sqo|sqq|sqr|sqs|sqt|squ|sra|srb|src|sre|srf|srg|srh|sri|srk|srl|srm|srn|sro|srq|srr|srs|srt|sru|srv|srw|srx|sry|srz|ssa|ssb|ssc|ssd|sse|ssf|ssg|ssh|ssi|ssj|ssk|ssl|ssm|ssn|sso|ssp|ssq|ssr|sss|sst|ssu|ssv|ssx|ssy|ssz|sta|stb|std|ste|stf|stg|sth|sti|stj|stk|stl|stm|stn|sto|stp|stq|str|sts|stt|stu|stv|stw|sty|sua|sub|suc|sue|sug|sui|suj|suk|sul|sum|suq|sur|sus|sut|suv|suw|sux|suy|suz|sva|svb|svc|sve|svk|svm|svr|svs|svx|swb|swc|swf|swg|swh|swi|swj|swk|swl|swm|swn|swo|swp|swq|swr|sws|swt|swu|swv|sww|swx|swy|sxb|sxc|sxe|sxg|sxk|sxl|sxm|sxn|sxo|sxr|sxs|sxu|sxw|sya|syb|syc|syd|syi|syk|syl|sym|syn|syo|syr|sys|syw|syx|syy|sza|szb|szc|szd|sze|szg|szl|szn|szp|szv|szw|taa|tab|tac|tad|tae|taf|tag|tai|taj|tak|tal|tan|tao|tap|taq|tar|tas|tau|tav|taw|tax|tay|taz|tba|tbb|tbc|tbd|tbe|tbf|tbg|tbh|tbi|tbj|tbk|tbl|tbm|tbn|tbo|tbp|tbq|tbr|tbs|tbt|tbu|tbv|tbw|tbx|tby|tbz|tca|tcb|tcc|tcd|tce|tcf|tcg|tch|tci|tck|tcl|tcm|tcn|tco|tcp|tcq|tcs|tct|tcu|tcw|tcx|tcy|tcz|tda|tdb|tdc|tdd|tde|tdf|tdg|tdh|tdi|tdj|tdk|tdl|tdm|tdn|tdo|tdq|tdr|tds|tdt|tdu|tdv|tdx|tdy|tea|teb|tec|ted|tee|tef|teg|teh|tei|tek|tem|ten|teo|tep|teq|ter|tes|tet|teu|tev|tew|tex|tey|tfi|tfn|tfo|tfr|tft|tga|tgb|tgc|tgd|tge|tgf|tgg|tgh|tgi|tgj|tgn|tgo|tgp|tgq|tgr|tgs|tgt|tgu|tgv|tgw|tgx|tgy|tgz|thc|thd|the|thf|thh|thi|thk|thl|thm|thn|thp|thq|thr|ths|tht|thu|thv|thw|thx|thy|thz|tia|tic|tid|tie|tif|tig|tih|tii|tij|tik|til|tim|tin|tio|tip|tiq|tis|tit|tiu|tiv|tiw|tix|tiy|tiz|tja|tjg|tji|tjl|tjm|tjn|tjo|tjs|tju|tjw|tka|tkb|tkd|tke|tkf|tkg|tkk|tkl|tkm|tkn|tkp|tkq|tkr|tks|tkt|tku|tkv|tkw|tkx|tkz|tla|tlb|tlc|tld|tlf|tlg|tlh|tli|tlj|tlk|tll|tlm|tln|tlo|tlp|tlq|tlr|tls|tlt|tlu|tlv|tlw|tlx|tly|tma|tmb|tmc|tmd|tme|tmf|tmg|tmh|tmi|tmj|tmk|tml|tmm|tmn|tmo|tmp|tmq|tmr|tms|tmt|tmu|tmv|tmw|tmy|tmz|tna|tnb|tnc|tnd|tne|tnf|tng|tnh|tni|tnk|tnl|tnm|tnn|tno|tnp|tnq|tnr|tns|tnt|tnu|tnv|tnw|tnx|tny|tnz|tob|toc|tod|toe|tof|tog|toh|toi|toj|tol|tom|too|top|toq|tor|tos|tou|tov|tow|tox|toy|toz|tpa|tpc|tpe|tpf|tpg|tpi|tpj|tpk|tpl|tpm|tpn|tpo|tpp|tpq|tpr|tpt|tpu|tpv|tpw|tpx|tpy|tpz|tqb|tql|tqm|tqn|tqo|tqp|tqq|tqr|tqt|tqu|tqw|tra|trb|trc|trd|tre|trf|trg|trh|tri|trj|trk|trl|trm|trn|tro|trp|trq|trr|trs|trt|tru|trv|trw|trx|try|trz|tsa|tsb|tsc|tsd|tse|tsf|tsg|tsh|tsi|tsj|tsk|tsl|tsm|tsp|tsq|tsr|tss|tst|tsu|tsv|tsw|tsx|tsy|tsz|tta|ttb|ttc|ttd|tte|ttf|ttg|tth|tti|ttj|ttk|ttl|ttm|ttn|tto|ttp|ttq|ttr|tts|ttt|ttu|ttv|ttw|tty|ttz|tua|tub|tuc|tud|tue|tuf|tug|tuh|tui|tuj|tul|tum|tun|tuo|tup|tuq|tus|tut|tuu|tuv|tuw|tux|tuy|tuz|tva|tvd|tve|tvk|tvl|tvm|tvn|tvo|tvs|tvt|tvu|tvw|tvy|twa|twb|twc|twd|twe|twf|twg|twh|twl|twm|twn|two|twp|twq|twr|twt|twu|tww|twx|twy|txa|txb|txc|txe|txg|txh|txi|txj|txm|txn|txo|txq|txr|txs|txt|txu|txx|txy|tya|tye|tyh|tyi|tyj|tyl|tyn|typ|tyr|tys|tyt|tyu|tyv|tyx|tyz|tza|tzh|tzj|tzl|tzm|tzn|tzo|tzx|uam|uan|uar|uba|ubi|ubl|ubr|ubu|uby|uda|ude|udg|udi|udj|udl|udm|udu|ues|ufi|uga|ugb|uge|ugn|ugo|ugy|uha|uhn|uis|uiv|uji|uka|ukg|ukh|ukl|ukp|ukq|uks|uku|ukw|uky|ula|ulb|ulc|ule|ulf|uli|ulk|ull|ulm|uln|ulu|ulw|uma|umb|umc|umd|umg|umi|umm|umn|umo|ump|umr|ums|umu|una|und|une|ung|unk|unm|unn|unp|unr|unu|unx|unz|uok|upi|upv|ura|urb|urc|ure|urf|urg|urh|uri|urj|urk|url|urm|urn|uro|urp|urr|urt|uru|urv|urw|urx|ury|urz|usa|ush|usi|usk|usp|usu|uta|ute|utp|utr|utu|uum|uun|uur|uuu|uve|uvh|uvl|uwa|uya|uzn|uzs|vaa|vae|vaf|vag|vah|vai|vaj|val|vam|van|vao|vap|var|vas|vau|vav|vay|vbb|vbk|vec|ved|vel|vem|veo|vep|ver|vgr|vgt|vic|vid|vif|vig|vil|vin|vis|vit|viv|vka|vki|vkj|vkk|vkl|vkm|vko|vkp|vkt|vku|vlp|vls|vma|vmb|vmc|vmd|vme|vmf|vmg|vmh|vmi|vmj|vmk|vml|vmm|vmp|vmq|vmr|vms|vmu|vmv|vmw|vmx|vmy|vmz|vnk|vnm|vnp|vor|vot|vra|vro|vrs|vrt|vsi|vsl|vsv|vto|vum|vun|vut|vwa|waa|wab|wac|wad|wae|waf|wag|wah|wai|waj|wak|wal|wam|wan|wao|wap|waq|war|was|wat|wau|wav|waw|wax|way|waz|wba|wbb|wbe|wbf|wbh|wbi|wbj|wbk|wbl|wbm|wbp|wbq|wbr|wbt|wbv|wbw|wca|wci|wdd|wdg|wdj|wdk|wdu|wdy|wea|wec|wed|weg|weh|wei|wem|wen|weo|wep|wer|wes|wet|weu|wew|wfg|wga|wgb|wgg|wgi|wgo|wgu|wgw|wgy|wha|whg|whk|whu|wib|wic|wie|wif|wig|wih|wii|wij|wik|wil|wim|win|wir|wit|wiu|wiv|wiw|wiy|wja|wji|wka|wkb|wkd|wkl|wku|wkw|wky|wla|wlc|wle|wlg|wli|wlk|wll|wlm|wlo|wlr|wls|wlu|wlv|wlw|wlx|wly|wma|wmb|wmc|wmd|wme|wmh|wmi|wmm|wmn|wmo|wms|wmt|wmw|wmx|wnb|wnc|wnd|wne|wng|wni|wnk|wnm|wnn|wno|wnp|wnu|wnw|wny|woa|wob|woc|wod|woe|wof|wog|woi|wok|wom|won|woo|wor|wos|wow|woy|wpc|wra|wrb|wrd|wrg|wrh|wri|wrk|wrl|wrm|wrn|wro|wrp|wrr|wrs|wru|wrv|wrw|wrx|wry|wrz|wsa|wsg|wsi|wsk|wsr|wss|wsu|wsv|wtf|wth|wti|wtk|wtm|wtw|wua|wub|wud|wuh|wul|wum|wun|wur|wut|wuu|wuv|wux|wuy|wwa|wwb|wwo|wwr|www|wxa|wxw|wya|wyb|wyi|wym|wyr|wyy|xaa|xab|xac|xad|xae|xag|xai|xaj|xak|xal|xam|xan|xao|xap|xaq|xar|xas|xat|xau|xav|xaw|xay|xba|xbb|xbc|xbd|xbe|xbg|xbi|xbj|xbm|xbn|xbo|xbp|xbr|xbw|xbx|xby|xcb|xcc|xce|xcg|xch|xcl|xcm|xcn|xco|xcr|xct|xcu|xcv|xcw|xcy|xda|xdc|xdk|xdm|xdy|xeb|xed|xeg|xel|xem|xep|xer|xes|xet|xeu|xfa|xga|xgb|xgd|xgf|xgg|xgi|xgl|xgm|xgn|xgr|xgu|xgw|xha|xhc|xhd|xhe|xhr|xht|xhu|xhv|xia|xib|xii|xil|xin|xip|xir|xis|xiv|xiy|xjb|xjt|xka|xkb|xkc|xkd|xke|xkf|xkg|xkh|xki|xkj|xkk|xkl|xkn|xko|xkp|xkq|xkr|xks|xkt|xku|xkv|xkw|xkx|xky|xkz|xla|xlb|xlc|xld|xle|xlg|xli|xln|xlo|xlp|xls|xlu|xly|xma|xmb|xmc|xmd|xme|xmf|xmg|xmh|xmj|xmk|xml|xmm|xmn|xmo|xmp|xmq|xmr|xms|xmt|xmu|xmv|xmw|xmx|xmy|xmz|xna|xnb|xnd|xng|xnh|xni|xnk|xnn|xno|xnr|xns|xnt|xnu|xny|xnz|xoc|xod|xog|xoi|xok|xom|xon|xoo|xop|xor|xow|xpa|xpc|xpe|xpg|xpi|xpj|xpk|xpm|xpn|xpo|xpp|xpq|xpr|xps|xpt|xpu|xpy|xqa|xqt|xra|xrb|xrd|xre|xrg|xri|xrm|xrn|xrq|xrr|xrt|xru|xrw|xsa|xsb|xsc|xsd|xse|xsh|xsi|xsj|xsl|xsm|xsn|xso|xsp|xsq|xsr|xss|xsu|xsv|xsy|xta|xtb|xtc|xtd|xte|xtg|xth|xti|xtj|xtl|xtm|xtn|xto|xtp|xtq|xtr|xts|xtt|xtu|xtv|xtw|xty|xtz|xua|xub|xud|xug|xuj|xul|xum|xun|xuo|xup|xur|xut|xuu|xve|xvi|xvn|xvo|xvs|xwa|xwc|xwd|xwe|xwg|xwj|xwk|xwl|xwo|xwr|xwt|xww|xxb|xxk|xxm|xxr|xxt|xya|xyb|xyj|xyk|xyl|xyt|xyy|xzh|xzm|xzp|yaa|yab|yac|yad|yae|yaf|yag|yah|yai|yaj|yak|yal|yam|yan|yao|yap|yaq|yar|yas|yat|yau|yav|yaw|yax|yay|yaz|yba|ybb|ybd|ybe|ybh|ybi|ybj|ybk|ybl|ybm|ybn|ybo|ybx|yby|ych|ycl|ycn|ycp|yda|ydd|yde|ydg|ydk|yds|yea|yec|yee|yei|yej|yel|yen|yer|yes|yet|yeu|yev|yey|yga|ygi|ygl|ygm|ygp|ygr|ygs|ygu|ygw|yha|yhd|yhl|yhs|yia|yif|yig|yih|yii|yij|yik|yil|yim|yin|yip|yiq|yir|yis|yit|yiu|yiv|yix|yiy|yiz|yka|ykg|yki|ykk|ykl|ykm|ykn|yko|ykr|ykt|yku|yky|yla|ylb|yle|ylg|yli|yll|ylm|yln|ylo|ylr|ylu|yly|yma|ymb|ymc|ymd|yme|ymg|ymh|ymi|ymk|yml|ymm|ymn|ymo|ymp|ymq|ymr|yms|ymt|ymx|ymz|yna|ynd|yne|yng|ynh|ynk|ynl|ynn|yno|ynq|yns|ynu|yob|yog|yoi|yok|yol|yom|yon|yos|yot|yox|yoy|ypa|ypb|ypg|yph|ypk|ypm|ypn|ypo|ypp|ypz|yra|yrb|yre|yri|yrk|yrl|yrm|yrn|yro|yrs|yrw|yry|ysc|ysd|ysg|ysl|ysn|yso|ysp|ysr|yss|ysy|yta|ytl|ytp|ytw|yty|yua|yub|yuc|yud|yue|yuf|yug|yui|yuj|yuk|yul|yum|yun|yup|yuq|yur|yut|yuu|yuw|yux|yuy|yuz|yva|yvt|ywa|ywg|ywl|ywn|ywq|ywr|ywt|ywu|yww|yxa|yxg|yxl|yxm|yxu|yxy|yyr|yyu|yyz|yzg|yzk|zaa|zab|zac|zad|zae|zaf|zag|zah|zai|zaj|zak|zal|zam|zao|zap|zaq|zar|zas|zat|zau|zav|zaw|zax|zay|zaz|zbc|zbe|zbl|zbt|zbw|zca|zch|zdj|zea|zeg|zeh|zen|zga|zgb|zgh|zgm|zgn|zgr|zhb|zhd|zhi|zhn|zhw|zhx|zia|zib|zik|zil|zim|zin|zir|ziw|ziz|zka|zkb|zkd|zkg|zkh|zkk|zkn|zko|zkp|zkr|zkt|zku|zkv|zkz|zle|zlj|zlm|zln|zlq|zls|zlw|zma|zmb|zmc|zmd|zme|zmf|zmg|zmh|zmi|zmj|zmk|zml|zmm|zmn|zmo|zmp|zmq|zmr|zms|zmt|zmu|zmv|zmw|zmx|zmy|zmz|zna|znd|zne|zng|znk|zns|zoc|zoh|zom|zoo|zoq|zor|zos|zpa|zpb|zpc|zpd|zpe|zpf|zpg|zph|zpi|zpj|zpk|zpl|zpm|zpn|zpo|zpp|zpq|zpr|zps|zpt|zpu|zpv|zpw|zpx|zpy|zpz|zqe|zra|zrg|zrn|zro|zrp|zrs|zsa|zsk|zsl|zsm|zsr|zsu|zte|ztg|ztl|ztm|ztn|ztp|ztq|zts|ztt|ztu|ztx|zty|zua|zuh|zum|zun|zuy|zwa|zxx|zyb|zyg|zyj|zyn|zyp|zza|zzj|";
	public $otherSubtags = "|aao|abh|abv|acm|acq|acw|acx|acy|adf|ads|aeb|aec|aed|aen|afb|afg|ajp|apc|apd|arb|arq|ars|ary|arz|ase|asf|asp|asq|asw|auz|avl|ayh|ayl|ayn|ayp|bbz|bfi|bfk|bjn|bog|bqn|bqy|btj|bve|bvl|bvu|bzs|cdo|cds|cjy|cmn|coa|cpx|csc|csd|cse|csf|csg|csl|csn|csq|csr|czh|czo|doq|dse|dsl|dup|ecs|esl|esn|eso|eth|fcs|fse|fsl|fss|gan|gds|gom|gse|gsg|gsm|gss|gus|hab|haf|hak|hds|hji|hks|hos|hps|hsh|hsl|hsn|icl|iks|ils|inl|ins|ise|isg|isr|jak|jax|jcs|jhs|jls|jos|jsl|jus|kgi|knn|kvb|kvk|kvr|kxd|lbs|lce|lcf|liw|lls|lsg|lsl|lso|lsp|lst|lsy|ltg|lvs|lzh|max|mdl|meo|mfa|mfb|mfs|min|mnp|mqg|mre|msd|msi|msr|mui|mzc|mzg|mzy|nan|nbs|ncs|nsi|nsl|nsp|nsr|nzs|okl|orn|ors|pel|pga|pgz|pks|prl|prz|psc|psd|pse|psg|psl|pso|psp|psr|pys|rms|rsi|rsl|rsm|sdl|sfb|sfs|sgg|sgx|shu|slf|sls|sqk|sqs|ssh|ssp|ssr|svk|swc|swh|swl|syy|tmw|tse|tsm|tsq|tss|tsy|tza|ugn|ugy|ukl|uks|urk|uzn|uzs|vgt|vkk|vkt|vsi|vsl|vsv|wuu|xki|xml|xmm|xms|yds|ygs|yhs|ysl|yue|zib|zlm|zmi|zsl|zsm|aa|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bl|bm|bn|bo|bq|br|bs|bt|bu|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cp|cr|cs|cu|cv|cw|cx|cy|cz|dd|de|dg|dj|dk|dm|do|dz|ea|ec|ee|eg|eh|er|es|et|eu|ez|fi|fj|fk|fm|fo|fr|fx|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|ic|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mf|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|qm..qz|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|ta|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|um|un|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xa..xz|yd|ye|yt|yu|za|zm|zr|zw|zz|001|002|003|005|009|011|013|014|015|017|018|019|021|029|030|034|035|039|053|054|057|061|142|143|145|150|151|154|155|419|adlm|afak|aghb|ahom|arab|aran|armi|armn|avst|bali|bamu|bass|batk|beng|bhks|blis|bopo|brah|brai|bugi|buhd|cakm|cans|cari|cham|cher|cirt|copt|cprt|cyrl|cyrs|deva|dsrt|dupl|egyd|egyh|egyp|elba|ethi|geok|geor|glag|goth|gran|grek|gujr|guru|hanb|hang|hani|hano|hans|hant|hatr|hebr|hira|hluw|hmng|hrkt|hung|inds|ital|jamo|java|jpan|jurc|kali|kana|khar|khmr|khoj|kitl|kits|knda|kore|kpel|kthi|lana|laoo|latf|latg|latn|leke|lepc|limb|lina|linb|lisu|loma|lyci|lydi|mahj|mand|mani|marc|maya|mend|merc|mero|mlym|modi|mong|moon|mroo|mtei|mult|mymr|narb|nbat|newa|nkgb|nkoo|nshu|ogam|olck|orkh|orya|osge|osma|palm|pauc|perm|phag|phli|phlp|phlv|phnx|piqd|plrd|prti|rjng|roro|runr|samr|sara|sarb|saur|sgnw|shaw|shrd|sidd|sind|sinh|sora|sund|sylo|syrc|syre|syrj|syrn|tagb|takr|tale|talu|taml|tang|tavt|telu|teng|tfng|tglg|thaa|thai|tibt|tirh|ugar|vaii|visp|wara|wole|xpeo|xsux|yiii|zinh|zmth|zsye|zsym|zxxx|zyyy|zzzz|1606nict|1694acad|1901|1959acad|1994|1996|abl1943|alalc97|aluku|ao1990|arevela|arevmda|baku1926|balanka|barla|basiceng|bauddha|biscayan|biske|bohoric|boont|colb1945|cornu|dajnko|ekavsk|emodeng|fonipa|fonnapa|fonupa|fonxsamp|hepburn|heploc|hognorsk|ijekavsk|itihasa|jauer|jyutping|kkcor|kociewie|kscor|laukika|lipaw|luna1918|metelko|monoton|ndyuka|nedis|newfound|njiva|nulik|osojs|oxendict|pamaka|petr1708|pinyin|polyton|puter|rigik|rozaj|rumgr|scotland|scouse|simple|solba|sotav|surmiran|sursilv|sutsilv|tarask|uccor|ucrcor|ulster|unifon|vaidika|valencia|vallader|wadegile|";

	public static function _init() {
		self::$logger = Logger::getLogger('Parser');
	}
	
	public static function getParser($markup, $contentType) {
		//if (true) { //self::is_HTML5($markup)) {
			self::$logger->debug(sprintf("Creating HTML5 parser. Content-type is: %s", $contentType == null ? 'null' : $contentType));
			//return new ParserDOM($markup, $contentType);
			return new ParserHTML5Lib($markup, $contentType);
		//}
			//self::$logger->debug(sprintf("Creating (X)HTML parser. Content-type is: %s", $contentType == null ? 'null' : $contentType));
			//return new ParserPHPQuery($markup, $contentType);
	}
	
	protected function __construct($markup, $contentType) {
		$this->markup = $markup;
		$this->findDoctype();
		$this->isXHTML1x = $this->isXHTML10 || $this->isXHTML11;
		// Auto determination of the mimetype based on the doctype if $contentType is null
		if ($contentType == null) {
			if ($this->isHTML || $this->isHTML5 || $this->isXHTML10) {
				$this->contentType = "text/html; charset=utf-8";
			} else {
				$this->contentType = "application/xhtml+xml; charset=utf-8";
				$this->isServedAsXML = true;
			}
		} else {
			$this->contentType = $contentType;
		}
		$this->mimetype = Utils::mimeFromContentType($this->contentType);
		if ($this->mimetype == 'application/xhtml+xml')
			$this->isServedAsXML = true;
		if ($this->isServedAsXML && $this->isHTML5) {
			$this->isXHTML5 = true;
			$this->isHTML5 = false;
		}
		$this->charset = Utils::charsetFromContentType($this->contentType);
		$this->getDirControls();
		$this->getI18nAttributes();
		$this->getI18nElements();
		$this->getLangTags();
		$this->getNumericEscapes();
	}
	
	protected function findDoctype() {
		if (preg_match("/<!DOCTYPE [^>]*DTD HTML/i", substr($this->markup, '0', Conf::get('perf_head_length')))) {
			$this->isHTML = true;
		} else if (preg_match("/<!DOCTYPE HTML>/i", substr($this->markup, '0', Conf::get('perf_head_length')))) { 
			$this->isHTML5 = true;
		} else if (preg_match("/<!DOCTYPE [^>]*DTD XHTML(\+[^ ]+)? 1.0[^>]+/i", substr($this->markup, '0', Conf::get('perf_head_length')), $matches)) {
			$this->isXHTML10 = true;
			if (preg_match('/RDFa/', $matches[0]))
				$this->isRDFa = true;
		} else if (preg_match("/<!DOCTYPE [^>]*DTD XHTML(\+[^ ]+)? 1.1[^>]+/i", substr($this->markup, '0', Conf::get('perf_head_length')), $matches)) {
			$this->isXHTML11 = true;
			if (preg_match('/RDFa/', $matches[0]))
				$this->isRDFa = true;
		} else {
			//TODO Add warning?
			$this->isHTML5 = true;
		}
	}
	
	public function doctype2String() {
		if ($this->isHTML)
			return 'HTML 4.01';
		if ($this->isXHTML10)
			if ($this->isRDFa)
				return 'XHTML+RDFa 1.0';
			else
				return 'XHTML 1.0';
		if ($this->isXHTML11)
			if ($this->isRDFa)
				return 'XHTML+RDFa 1.1';
			else
				return 'XHTML 1.1';
		if ($this->isXHTML5)
			return 'XHTML 5';
		if ($this->isHTML5)
			return 'HTML5';
		self::$logger->error("No doctype has been defined. This shouldn't happen.");
		return "N/A";
	}
	
	public function HTMLTag() {
		return $this->dumpTag($this->document->getElementsByTagName('html')->item(0));
	}
	
	public function XMLDeclaration() {
		preg_match('/<\?xml[^>]+>/i', substr($this->markup, '0', Conf::get('perf_head_length')), $matches);
		return isset($matches[0]) ? $matches[0] : null;
	}
	
	public function getHTMLTagAttr($name, $xmlNamespace = false) {
		//if ($this->document->getElementsByTagName('html')->item(0) == null)
		//	return null;
		$htmlAttrs = $this->document->getElementsByTagName('html')->item(0)->attributes;//$this->document->documentElement->attributes;
		if ($htmlAttrs == null)
			return null;
		if ($xmlNamespace)
			$attr = $htmlAttrs->getNamedItemNS('http://www.w3.org/XML/1998/namespace', $name);
		else
			$attr = $htmlAttrs->getNamedItemNS(null, $name);
		return ($attr != null) ? $attr->value : null;
	}
	
	// does not return null! should check if empty and not if null
	public function getMetaWithAttr($name) {
		$metas = $this->document->getElementsByTagName("meta");
		// FIXME: case sensitive. Should iterate over attributes and do strcasecmp.
		foreach ($metas as $meta) {
			if (($charset = $meta->attributes->getNamedItem($name)) != null) {
				$result[] = array ( 
					'code'   => $this->dump($meta),
					'values' => $charset->value
				);
			}
		}
		return isset($result) ? $result : array();
	}
	
	// does not return null! should check if empty and not if null
	public function getHTTPEquivMeta($name, $codeFunction = null) {
		$metas = $this->document->getElementsByTagName("meta");
		foreach ($metas as $meta) {
			if (($equivParam = $meta->attributes->getNamedItem('http-equiv')) != null) {
				if (strcasecmp($equivParam->value, $name) == 0) {
					$_code = $this->dump($meta);
					if (($contentParam = $meta->attributes->getNamedItem('content')) == null)
						$_values = null;
					else
						$_values = $codeFunction == null ? $contentParam->value : call_user_func($codeFunction, $contentParam->value);
					$result[] = array ( 
							'code'   => $_code,
							'values' => $_values
						);
				}
			}
		}
		return isset($result) ? $result : array();
	}
	
	public function getMetaCharset() {
		return $this->getMetaWithAttr('charset');
	}
	
	public function getMetaContentType() {
		return $this->getHTTPEquivMeta('Content-Type', 'Utils::charsetFromContentType');
	}
	
	public function getMetaContentLanguage() {
		return $this->getHTTPEquivMeta('Content-Language', 'Utils::getValuesFromCSString');
	}
	
	public function getNodesWithAttr($attr, $xmlNamespace = false) {
		$t = &$this;
		$test = function($node) use (&$result, $t, $attr, $xmlNamespace) {
			if ($node != null && $node->hasAttributes()) {
				$a = !$xmlNamespace ? $node->attributes->getNamedItemNS(null, $attr) : $node->attributes->getNamedItemNS('http://www.w3.org/XML/1998/namespace', $attr);
				if ($a != null) {
					$result[] = array(
						'code' => $t->dumpTag($node),
						'values' => count(($p = array_values(array_filter(Utils::arrayTrim(preg_split('/[ ]+/', $a->value)))))) == 1 ? $p[0] : $p // array_filter with no callback parameter will remove empty elements
					);
				}
			}
		};
		$html = $this->document->getElementsByTagName('html')->item(0);
		$this->iterate($test, $html, true);
		return $result;
	} 
	
	public function getElementsByTagName($tagName) {
		return $this->document->getElementsByTagName($tagName);
	}
	
	public function dump($node){
	    return $this->document->saveXML($node);
	}
	
	// Only dumps the opening tag of $node
	public function dumpTag($node){
	    preg_match('/^<[^>]+>/i', $this->document->saveXML($node), $matches);
	    return isset($matches[0]) ? $matches[0] : null;
	}
	
	protected function iterate($callback, $node, $includeParentNode = false) {
		if ($includeParentNode)
			$callback($node);
		if ($node == null)
			return;
		foreach ($node->childNodes as $child) {
			$callback($child);
			if ($child->hasChildNodes())
				$this->iterate($callback, $child);
		}
	}
	
	private function getDirControls() {
		if (preg_match_all('/(&rlm;)|(&lrm;)|(&#8206;)|(&#8207;)|(&#8234;)|(&#8235;)|(&#8236;)|(&#8237;)|(&#8238;)|(&#8294;)|(&#8295;)|(&#8296;)|(&#8297;)|(&#x200E;)|(&#x200F;)|(&#x202A;)|(&#x202B;)|(&#x202C;)|(&#x202D;)|(&#x202E;)|(&#x2066;)|(&#x2067;)|(&#x2068;)|(&#x2069;)|(‎)|(‏)|(‪)|(‫)|(‬)|(‭)|(‮)|(⁦)|(⁧)|(⁨)|(⁩)/', $this->markup, $foundEntities)) {
			$entityList = array_count_values($foundEntities[0]);
			$dirControls = array('rlm'=>0,'&rlm'=>0,'#rlm'=>0,'lrm'=>0,'&lrm'=>0,'#lrm'=>0,'lre'=>0,'#lre'=>0, 'rle'=>0,'#rle'=>0, 'pdf'=>0,'#pdf'=>0, 'rli'=>0,'#rli'=>0, 'lri'=>0,'#lri'=>0, 'fsi'=>0,'#fsi'=>0, 'pdi'=>0,'#pdi'=>0, 'rlo'=>0,'#rlo'=>0, 'lro'=>0,'#lro'=>0);
			// merge the results for hex and dec escapes
			foreach ($entityList as $key => $val) {
				switch ($key) {
					case '‏': $dirControls['rlm'] += $val; break;
					case '&#8207;';
					case '&#x200F;':$dirControls['#rlm'] += $val; break;
					case '&rlm;':$dirControls['&rlm'] += $val; break;
					case '‎': $dirControls['lrm'] += $val; break;
					case '&#8206;';
					case '&#x200E;':$dirControls['#lrm'] += $val; break;
					case '&lrm;':$dirControls['&lrm'] += $val; break;
					case '‪': $dirControls['lre'] += $val; break;
					case '&#8234;';
					case '&#x202A;':$dirControls['#lre'] += $val; break;
					case '‫': $dirControls['rle'] += $val; break;
					case '&#8235;';
					case '&#x202B;': $dirControls['#rle'] += $val; break;
					case '‬': $dirControls['pdf'] += $val; break;
					case '&#8236;';
					case '&#x202C;': $dirControls['#pdf'] += $val; break;

					case '‭': $dirControls['lro'] += $val; break;
					case '&#8237;';
					case '&#x202D;': $dirControls['#lro'] += $val; break;
					case '‮': $dirControls['rlo'] += $val; break;
					case '&#8238;';
					case '&#x202E;': $dirControls['#rlo'] += $val; break;

					case '⁦': $dirControls['lri'] += $val; break;
					case '&#8294;';
					case '&#x2066;': $dirControls['#lri'] += $val; break;
					case '⁧': $dirControls['rli'] += $val; break;
					case '&#8295;';
					case '&#x2067;': $dirControls['#rli'] += $val; break;

					case '⁨': $dirControls['fsi'] += $val; break;
					case '&#8296;';
					case '&#x2068;': $dirControls['#fsi'] += $val; break;
					case '⁩': $dirControls['pdi'] += $val; break;
					case '&#8297;';
					case '&#x2069;': $dirControls['#pdi'] += $val; break;
				}
			}
		$this->dirControls = $dirControls;
		}
	}
	
	private function getI18nAttributes() {
		$i18nAttributes = array();
		$dir = $this->getNodesWithAttr('dir');
		if ($dir && count($dir)>0) $i18nAttributes['dir'] = count($dir);
		$dirname = $this->getNodesWithAttr('dirname');
		if ($dirname && count($dirname)>0) $i18nAttributes['dirname'] = count($dirname);
		$translate = $this->getNodesWithAttr('translate');
		if ($translate && count($translate)>0) $i18nAttributes['translate'] = count($translate);
		$date = $this->getNodesWithAttr('date');
		if ($date && count($date)>0) $i18nAttributes['date'] = count($date);
		$datetime = $this->getNodesWithAttr('datetime');
		if ($datetime && count($datetime)>0) $i18nAttributes['datetime'] = count($datetime);
		$inputmode = $this->getNodesWithAttr('inputmode');
		if ($inputmode && count($inputmode)>0) $i18nAttributes['inputmode'] = count($inputmode);
		$spellcheck = $this->getNodesWithAttr('spellcheck');
		if ($spellcheck && count($spellcheck)>0) $i18nAttributes['spellcheck'] = count($spellcheck);
		$lang = $this->getNodesWithAttr('lang');
		if ($lang && count($lang)>0) $i18nAttributes['lang'] = count($lang);
		$hreflang = $this->getNodesWithAttr('hreflang');
		if ($hreflang && count($hreflang)>0) $i18nAttributes['hreflang'] = count($hreflang);
		$srclang = $this->getNodesWithAttr('srclang');
		if ($srclang && count($srclang)>0) $i18nAttributes['srclang'] = count($srclang);
		$this->i18nAttributes = $i18nAttributes;
		}
	
	private function getI18nElements() {
		$i18nElements = array();
		$ruby = $this->document->getElementsByTagName('ruby');
		if ($ruby->length>0) $i18nElements['ruby'] = $ruby->length;
		$rb = $this->document->getElementsByTagName('rb');
		if ($rb->length>0) $i18nElements['rb'] = $rb->length;
		$rt = $this->document->getElementsByTagName('rt');
		if ($rt->length>0) $i18nElements['rt'] = $rt->length;
		$rp = $this->document->getElementsByTagName('rp');
		if ($rp->length>0) $i18nElements['rp'] = $rp->length;
		$rtc = $this->document->getElementsByTagName('rtc');
		if ($rtc->length>0) $i18nElements['rtc'] = $rtc->length;
		$wbr = $this->document->getElementsByTagName('wbr');
		if ($wbr->length>0) $i18nElements['wbr'] = $wbr->length;
		$time = $this->document->getElementsByTagName('time');
		if ($time->length>0) $i18nElements['time'] = $time->length;
		$u = $this->document->getElementsByTagName('u');
		if ($u->length>0) $i18nElements['u'] = $u->length;
		$bdi = $this->document->getElementsByTagName('bdi');
		if ($bdi->length>0) $i18nElements['bdi'] = $bdi->length;
		$bdo = $this->document->getElementsByTagName('bdo');
		if ($bdo->length>0) $i18nElements['bdo'] = $bdo->length;
		$q = $this->document->getElementsByTagName('q');
		if ($q->length>0) $i18nElements['q'] = $q->length;

		$this->i18nElements = $i18nElements;
		}
	
	private function getLangTags() {
		// returns a simple array of unique language tag values in the document
		$langTags = array_merge((array) $this->getNodesWithAttr('lang'), (array) $this->getNodesWithAttr('lang', true));
		$tagList = array();
		foreach ($langTags as $val) {
			if (is_array($val['values'])) $val['values'] = implode($val['values']);
			$tagList[] = strtolower($val['values']);
			}
		$this->langTags = array_unique($tagList);
	}
	
	
	private function getNumericEscapes() {
		// returns an array of unique keys for numeric character references found, with frequency as value
		// the keys are numeric, and hex and dec escapes are merged
		preg_match_all('/&#([0-9]+);/', $this->markup, $dec);
		preg_match_all('/&#x([a-fA-F0-9]+);/', $this->markup, $hex);
		for ($i=0;$i<count($hex[1]);$i++){
			$hex[1][$i] = hexdec($hex[1][$i]);
			}
		$result = array_count_values(array_merge($hex[1], $dec[1]));
		$this->numEscapes = $result;
		}
	
}

Parser::_init();
