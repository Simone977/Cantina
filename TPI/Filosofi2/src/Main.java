
public class Main {
    public static void main(String[] args) {
        int i;
        final int n=10;
        int TempoMax = 10000;
        Tavola M=new Tavola();
        Filosofo []F=new Filosofo[n];
        for(i=0;i<n;i++)
            F[i]=new Filosofo(M, i);
        for(i=0;i<n;i++)
            F[i].start();

        try{ Thread.sleep(TempoMax); }catch(Exception e){};

        for(i=0;i<n;i++)
            F[i].interrupt();
        System.out.println("fine");
    }
}