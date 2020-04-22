import java.util.Random;

public class Main {
    static final int n=3;
    static Thread[] scrittore = new Thread[3];
    static Lettore[] lettore = new Lettore[3];

    public static void main(String[] args) {


        for(int i=0; i<n; i++){
            scrittore[i]= new Scrittore();
            scrittore[i].start();
            lettore[i]= new Lettore();
            lettore[i].start();
        }

        try{ Thread.sleep(30000); }catch(Exception e){};

        for(int i=0; i<n; i++){
            scrittore[i].stop();
            lettore[i].stop();
        }
        System.out.println("fine");
    }
}
