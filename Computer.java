
package prova;


public class Computer {
    private String marca;
    private String modello;
    private int vProcessore;
    private int ram;
    private int codice;
    
    public Computer( String marca,String modello, int vProcessore, int rem, int codice ){
        this.marca = marca;
        this.modello = modello;
        this.vProcessore = vProcessore;
        this.ram = rem;
        this.codice = codice;
    }
    
    public Computer( Computer copia ){
        this.marca = copia.marca;
        this.modello = copia.modello;
        this.vProcessore = copia.vProcessore;
        this.ram = copia.ram;
        this.codice = copia.codice;
    }
    public boolean equals(Computer uno, Computer due){
        return  uno.marca.equals(due.marca) && 
                uno.modello.equals(due.modello) && 
                uno.vProcessore == due.vProcessore &&
                uno.ram == due.ram &&
                uno.codice == due.codice;
    } 

    public String getMarca() { return marca; }
    public String getModello() { return modello; }
    public String getvProcessore() { return "" + vProcessore; }
    public String getCodice() { return "" + codice; }
    public String getRam() { return "" + ram; }
    
}
