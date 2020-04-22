public class Lettore extends Thread {

    String messaggio;

    public Lettore(){
    }

    public void setMessaggio (String messaggio){
        this.messaggio=messaggio;
    }

    public void run(){
        while( !currentThread().isInterrupted() ){

            try { Thread.sleep(1000); } catch (InterruptedException e) {}
            System.out.println( currentThread().getId() + ": " + this.messaggio );
            try { Thread.sleep(3000); } catch (InterruptedException e) {}
        }



    }
}
