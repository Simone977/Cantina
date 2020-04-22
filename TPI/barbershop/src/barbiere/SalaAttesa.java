package barbiere;
import java.util.ArrayList;

public class SalaAttesa extends Thread {
    int n;
    ArrayList<Cliente> array;
    int x;

    public SalaAttesa(int n){
        this.n=n;
        array = new ArrayList<>();
        x=0;
    }

    public void aggiungiCliente(Cliente cliente){
        if(x<n){
            System.out.println("Arrivato: " + cliente.getName());
            array.add(cliente);
            x++;
        }
        else{
            System.out.println("Sala piena");
        }
    }
    public boolean vuoto(){

        if (array.isEmpty())
            System.out.print("");

        return array.isEmpty();
    }

    public Cliente getCliente(){
        Cliente cliente = array.get(0);
        array.remove(0);
        x--;
        return cliente;
    }
}
