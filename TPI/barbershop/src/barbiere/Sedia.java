package barbiere;

public class Sedia extends Thread {
    Cliente servito;
    SalaAttesa sala;

    public Sedia(SalaAttesa sala){
        this.sala = sala;
    }

    public void run(){
        System.out.println("Barbiere: Dorme");
        while (true) {

            if ( !sala.vuoto() ) {
                System.out.println("Barbiere: Si sveglia");
                servito = sala.getCliente();
                System.out.println("Taglio: " + servito.getName());
                try {
                    sleep((int) servito.tempo * 1000);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
                System.out.println("Finito: " + servito.getName());
                servito = null;
                System.out.println("Barbiere: Dorme");
            }
        }
    }

}
