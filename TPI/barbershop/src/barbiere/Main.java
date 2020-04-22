package barbiere;

public class Main {
    public static void main(String[] args) {
        final int n=10;

        //creazione e start dei Thread
        Cliente[] clienti= new Cliente[n+5];
        SalaAttesa sala= new SalaAttesa(n);
        sala.start();

        Sedia sedia = new Sedia(sala);
        sedia.start();
        for( int i=0; i<n+5; i++ ){
            clienti[i] = new Cliente(sala);
            clienti[i].start();
        }

        //Tempo di svolgimento del programma
        try {
            Thread.sleep(500*1000);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

        //interruzione dei Thread
        sala.interrupt();

        sedia.interrupt();
        for( int i=0; i<n+5; i++ ){
            clienti[i].interrupt();
        }

    }
}
