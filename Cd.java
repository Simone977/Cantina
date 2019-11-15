package cd;

public class Cd{
    private String titolo;
    private String autore;
	  private int durata;

    public Cd(String titolo, String autore, int durata){
        this.titolo = titolo;
        this.autore = autore;
        this.durata = durata;
    }
    public Cd(Cd copia){
        this.titolo = copia.titolo;
        this.autore = copia.autore;
        this.durata = copia.durata;
    }
    public boolean equals(Cd due){
        return this.titolo.equals(due.titolo) && this.autore.equals(due.autore) && this.durata == due.durata;
    }
    
    public String getTitolo() {
    	return this.titolo;
    }
}
