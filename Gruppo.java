/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package prova;

/**
 *
 * @author simonericci
 */
public class Gruppo {
    Computer[] computers;
    private final int N=5;
    
    public Gruppo(){
        computers = new Computer[N];
    }
    
    public void insComputer(Computer comp){
        int pos=0;
        while( pos<N ) { 
            if(this.computers[pos] == null){
                this.computers[pos]=comp;
                System.out.println("posizione utilizzata: " + pos);
                return;
            }
            else  
                pos++; 
        }
        System.out.println("posizioni occupate");          
    }
    
    
    
}
