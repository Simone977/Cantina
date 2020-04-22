import java.util.Random;
public class Scrittore extends Thread {

    public Scrittore(){
    }

    public void run(){
        while( !currentThread().isInterrupted() ){

            Random rand = new Random();
            int a = rand.nextInt(3);
            Main.lettore[a].setMessaggio("ciao, da:" + currentThread().getId() );

            try { Thread.sleep(3000); } catch (InterruptedException e) {}

        }



    }
}

