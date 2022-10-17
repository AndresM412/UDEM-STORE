import random
import Lista_Enlazada

class Wordle_Logica:
    global L1
    global L2
    global L3
    L1=Lista_Enlazada.LinkedList()
    L2=Lista_Enlazada.LinkedList()
    L3=Lista_Enlazada.LinkedList()
    def Bienvenida():
        s=str(input("Por favor ingrese la letra s: "))
        if s=="S"or s=="s":
            print("\n¡Has ingresado a Wordle!, cargando palabra.... \n")
            Palabra = Wordle_Logica.Pasar_Palabra_A_LinkedList()
            print("¡Palabra cargada con exito! ;)\n")
            
        else:
            print("Has ingresado un caracter erroneo, intentalo de nuevo! ")
            return Wordle_Logica.Bienvenida()
        
    def Pasar_Palabra_A_LinkedList():
        Palabras=["bóxer","busto","burro","burla","bufón","bufet","bueno","bucal","bingo","códex"]
        Palabra_Elegida=random.choice(Palabras)
        for i in Palabra_Elegida:
           L1.add_at_head(i)
        L1.reverse()
        L1.traverse()  #Para mostrar la palabra
    
    def Validar_palabra_intento():
        w=input("Por favor ingrese una palabra... ")
        w=w.lower()
        if w.isalpha()!=True:
            print("¡Por favor ingresa una palabra valida!")
            return Wordle_Logica.Validar_palabra_intento()
        pass
        
        if len(w)!=5:
            print("¡Por favor ingresa una palabra de 5 letras!")
            return Wordle_Logica.Validar_palabra_intento()
        else:
            print("Palabra ingresada con exito")
            for i in w:
             L2.add_at_head(i)
            L2.reverse()
            L2.traverse()

    def Palabras_Iguales():
        contL1=1
        contL2=1
        ActualL2=L2.head
        ActualL1=L1.head
        j=1
        while j <=5:
            ActualL2=L2.head 
            for i in range(5):
             if ActualL1.value != ActualL2.value:
                print(ActualL2.value)
                ActualL2=ActualL2.next
                print("No existe😓 \n")
                contL1+=1
             elif ActualL1.value == ActualL2.value:
                print(ActualL2.value)
                if contL1!=contL2:
                    print("Existe, pero no en esta posicion \n")
                else:
                    print("Existe y en la posicion correcta \n")
                    L3.add_at_head(ActualL2.value)
                ActualL2=ActualL2.next
                contL1+=1
                
            L3.reverse()
            L3.traverse()
            print("------------------------------------------------")   
            print("------------------------------------------------") 
            ActualL1=ActualL1.next    
            j = j+1                     
            