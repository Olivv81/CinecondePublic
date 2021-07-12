//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Detection du Navigateur
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

var Nav = navigator;
var Agent = ' ' + Nav.userAgent.toLowerCase();
var Version = Nav.appVersion;

var Netscape = Agent.indexOf('mozilla') > 0;
if (Agent.indexOf('compatible') > 0) Netscape = false;
var Explorer = Agent.indexOf('msie') > 0;
var VMajeure = parseInt( Version );
var VMineure = parseFloat( Version );

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Fenetre Surgissante
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function Fenetre(URL, Lg, Ht, Px, Py, Scroll, Resize, Nom, Adresse, Bouton, Menu )
{
	if (typeof(Lg)!="number") Lg=320;
	if (typeof(Ht)!="number") Ht=430;

	if (typeof(Px)!="number") Px=0;
	if (typeof(Py)!="number") Py=0;

	if ((typeof(Scroll)!="number") || Scroll!=1) Scroll="no"; else Scroll="yes";
	if ((typeof(Resize)!="number") || Resize!=1) Resize="no"; else Resize="yes";
	
	if (typeof(Nom)!="string") Nom="FenAlloCine" + NomFen(URL);
	
	if ((typeof(Adresse)!="number") || Adresse!=1) Adresse="no"; else Adresse="yes";
	if ((typeof(Bouton)!="number") || Bouton!=1) Bouton="no"; else Bouton="yes";
	if ((typeof(Menu)!="number") || Menu!=1) Menu="no"; else Menu="yes";
	
	FenAC = window.open(URL,Nom,"toolbar=" + Bouton + ",scrollbars=" + Scroll + ",resizable=" + Resize + ",width=" + Lg + 
	",height=" + Ht + ",left=" + Px + ",top=" + Py + ",location=" + Adresse + ",menubar=" + Menu )
	
	if (navigator.appName == "Netscape" || (navigator.userAgent.indexOf("MSIE") > 0  && parseInt(navigator.appVersion.substring(0,1)) > 3))
		FenAC.focus();
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Remplacement de chaine pour nommer les fenetres
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function NomFen(Adresse)
{
	Adresse = Remplace(Adresse, 'http://', '');
	Adresse = Remplace(Adresse, '/', '');
	Adresse = Remplace(Adresse, '.', '');
	Adresse = Remplace(Adresse, '=', '');
	Adresse = Remplace(Adresse, '?', '');
	Adresse = Remplace(Adresse, '&', '');
	Adresse = Remplace(Adresse, '-', '');
	Adresse = Remplace(Adresse, ';', '');
	Adresse = Remplace(Adresse, '%', '');
	Adresse = Remplace(Adresse, '|', '');
	Adresse = Remplace(Adresse, '_', '');
	Adresse = Remplace(Adresse, '@', '');
	
	return(Adresse.slice(-15));
}

function Remplace(Chaine, Ancien, Nouveau)
{
 if (Ancien != Nouveau && Ancien.length > 0 )
 {
  Pos = Chaine.indexOf(Ancien, 0);
  
  while (Pos >= 0)
  {
   Chaine =  Chaine.substring(0, Pos) + Nouveau + Chaine.substring(Pos + Ancien.length, Chaine.length); 
   Pos = Chaine.indexOf(Ancien, 0);
  }
 }
 return Chaine;
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Ouverture AlloCiné Vision pour partenaires avec player standard en flash
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function FenACVision(CMedia, Emmission_old, TimeCode_old, NomPartenaire)
{
	if ( ((typeof(CMedia) != "number") || (CMedia == "0")) ) 
		CMedia = ""; 
	
	if (typeof(NomPartenaire) != "string") NomPartenaire = "";

	FenACV = window.open("http://www.allocine.fr/_video/partner/" + NomPartenaire + "/" + CMedia, "ACVision","toolbar=no,scrollbars=no,resizable=no,width=600,height=400,left=250,top=150,location=0,menubar=0");

	if (navigator.appName == "Netscape" || (navigator.userAgent.indexOf("MSIE") > 0  && parseInt(navigator.appVersion.substring(0,1)) > 3))
		FenACV.focus();
}



//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	URL spécifiée en paramètre (Bandeaux Pub)
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function FenACVisionURL_old(URL)
{
	FenACV = window.open(URL,"PopUpACVision","toolbar=no,scrollbars=no,resizable=yes,width=604,height=480,left=250,top=150,location=0,menubar=0");
	
	if (navigator.appName == "Netscape" || (navigator.userAgent.indexOf("MSIE") > 0  && parseInt(navigator.appVersion.substring(0,1)) > 3))
		FenACV.focus();
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Fenetre Surgissante / Test Cookie
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Cook : Nom du cookie
//
//	Longevite : Durée avant expiration en Heures

function FenetreCook(Cook, Longevite, URL, Lg, Ht, Px, Py, Scroll, Resize, Nom)
{
	var CVal = GetCookie(Cook);
	
	if (CVal == null) 
	{
		SetCookie(Cook, 1, Longevite);
		 
		Fenetre(URL, Lg, Ht, Px, Py, Scroll, Resize, Nom)
	}	
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Nom de domaine principal
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function DomaineCookie()
{
	var Domaine = window.location.hostname;
	
	if (Domaine.indexOf('.allocine') == -1 && Netscape && VMajeure == 4)
		return ("");
	else
		return ('.'+Domaine.substr(Domaine.indexOf('allocine')));
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//
//	Gestion des Cookies
//
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

// Longévité en Jours

function SetCookie(NomCookie, Valeur, Longevite) 
{
	SetCookieM(NomCookie, Valeur, Longevite * 60 * 24 );
}

// Longévité en Minutes

function SetCookieM(NomCookie, Valeur, LongeviteM) 
{
	var Now = new Date();
	
	if (typeof(Longevite)!="number") Longevite=1;
	
	Now.setTime(Now.getTime() + LongeviteM * 1000 * 60 );
	
	var Domaine = DomaineCookie();
	
	if (Domaine != "") Domaine = "; domain=" + Domaine;
	
  var CookieCrt = NomCookie + "=" + escape(Valeur) + "; expires=" + Now.toGMTString() + Domaine + "; path=/";
  
  document.cookie = CookieCrt;
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function GetCookie(NomCookie) 
{
  var dc = document.cookie;
  var prefix = NomCookie + "=";
  var begin = dc.indexOf("; " + prefix);
  
  if (begin == -1) 
  {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } 
  else
    begin += 2;
  
  var end = document.cookie.indexOf(";", begin);
  
  if (end == -1) 
		end = dc.length;
  
  return unescape(dc.substring(begin + prefix.length, end));
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-