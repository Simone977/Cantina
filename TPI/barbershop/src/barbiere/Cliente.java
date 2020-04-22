package barbiere;
import java.util.Random;


public class Cliente extends Thread {
    final int x=2;
    final float y=x/2;
    float tempo;
    SalaAttesa sala;
    Random rand = new Random();

    public Cliente(SalaAttesa sala){
        this.tempo = rand.nextInt()%x + y;
        this.sala = sala;
    }

    public void run(){
        while(true) {
            int tempo = Math.abs(rand.nextInt()) % 50 + 5;
            try {
                this.sleep(tempo * 1000);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
            System.out.println(this.getName()+" bussa");
            sala.aggiungiCliente(this);
        }
    }

}
