// ---------------------------------------------------
// 2012 (c) Bc. Tomas Frtala
// 
// Tento skript zkompiluje dynamicku kniznicu 
// v prostredi SCILAB. Vysledna dyn.kniz. umozni
// komunikovat s USB rozhranim
// ---------------------------------------------------

// skompilovanie dynamickej kniznice pre termo sustavu
ilib_for_link('termo','termo.o',[],'c','Makelib','loader.sce','','','-lusb');

// ukoncenie behu kompilacie
exit
