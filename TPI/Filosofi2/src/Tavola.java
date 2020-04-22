
import java.util.concurrent.locks.Condition;
import java.util.concurrent.locks.Lock;
import java.util.concurrent.locks.ReentrantLock;;

public class Tavola { //Costanti:
    private final int NF = 10; // costante: num forchette/filosofi
    //Dati:
    private int[] forchette = new int[NF]; //num forchette disponibili per ogni filosofo i
    private Lock lock = new ReentrantLock();
    private Condition[] codaF = new Condition[NF];//1 coda per ogni filosofo i

    //Costruttore:
    public Tavola() {
        int i;
        for (i = 0; i < NF; i++)
            codaF[i] = lock.newCondition();
        for (i = 0; i < NF; i++)
            forchette[i] = 2;
    }
    public void prendiForchette(int i)throws
            InterruptedException
    { lock.lock();
        try
        {
            while (forchette[i]!=2)
                codaF[i].await();
            forchette[sinistra(i)]--;
            forchette[destra(i)]--;
        } finally{ lock.unlock();}
        return;
    }

    public void rilasciaForchette(int
                                          i)throws InterruptedException
    { lock.lock();
        try
        {
            forchette[sinistra(i)]++;
            forchette[destra(i)]++;
            if (forchette[sinistra(i)]==2)
                codaF[sinistra(i)].signal();
            if (forchette[destra(i)]==2)
                codaF[destra(i)].signal();
        } finally{ lock.unlock();}
        return;
    }

    int destra(int i)
    { int ret;
        ret=(i==0? NF-1:(i-1));
        return ret;
    }
    int sinistra(int i)
    { int ret;
        ret=(i+1)%NF;
        return ret;
    }

}