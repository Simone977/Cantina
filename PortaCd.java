package cd;

public class PortaCd{
  private final int DIM=50;
  private Cd[] cds;

  public PortaCd(){
    	cds = new Cd[DIM];
  }

  public PortaCd(PortaCd copia){
      this.cds = copia.cds;
  }

  public int setCd(Cd cd){
      for (int i=0; i<DIM; i++) {
        	if(this.cds[i] != null) {
          		this.cds[i] = cd;
          		return i;
          }
      }
          return -1;
  }

  public Cd getCD(int pos){
      if( pos<0 || pos > DIM || this.cds[pos] == null)
          return null;
      return this.cds[pos];
  }

  public int ConfrontaCollezione(PortaCd due){
      int n=0, i , j;
      for( i=0; i<DIM-1; i++){
          if(this.cds[i] != null){
              for( j=0; j<DIM-1; j++){
                  if(this.cds[i].equals(due.cds[j]))
                      n++;
              }
          }
      }
      return n;
  }

  public int cercaPerTitolo( String titolo){
      for(int i=0; i<DIM-1; i++){
          if(this.cds[i].getTitolo().equals(titolo))
              return i;
      }
      return -1;
  }

  public void killCd(int pos){
      this.cds[pos]= null;
  }

  public int contaCd(){
      int n=0;
      for( int i=0; i<DIM-1; i++){
          if( this.cds[i] !=null)
              n++;
      }
      return n;
  }

  public String toString(){
      String titoli= "titoli:";
      for( int i=0; i<DIM-1; i++){
          if(this.cds[i]!=null)
              titoli = titoli + "\t" + this.cds[i].getTitolo() + "\n";
      }
      return titoli;
  }










}
