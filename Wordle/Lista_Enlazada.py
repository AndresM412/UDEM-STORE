#Lista e
class Node:
    def __init__(self, value):
     self.value = value
     self.next = None

class LinkedList:
  def __init__(self):
    self.head = None
    
  def add_at_head(self, value):
    node = Node(value)
    if(self.head is None):
      self.head = node
    else:
      node.next = self.head
      self.head = node #definí mi nueva cabeza

  def traverse(self):
    current_node = self.head
    while(current_node is not None):
      print(current_node.value, end="->")
      current_node = current_node.next
    print()

  def rec_traverse(self, current_node):
    if(current_node is not None):
      print(current_node.value)
      self.rec_traverse(current_node.next)

  def get_size(self):
    current_node = self.head
    size = 0
    if(current_node is None):
      return size
    else:
      while(current_node is not None):
        current_node = current_node.next
        size += 1
    return size

  def delete_at_pos(self, pos):
    if(0>pos or pos>=self.get_size()):
      print("Posición inválida...")
    else:
      if(pos==0):
        aux_node = self.head
        self.head = self.head.next
        aux_node.next = None
      else:
        current_node = self.head
        for i in range(0,pos-1):
          current_node = current_node.next
        aux_node = current_node.next
        current_node.next = current_node.next.next
        aux_node.next = None

  def remove(self, value):
        if self.head.value == value:
            self.head = self.head.next
            return ""
        else:
            current = self.head
            while current.next.value != value:
                current = current.next
                borrar = current.next
                current.next = borrar.next
                return ""

 # Function to reverse the linked list
  def reverse(self):
    prev = None
    current = self.head
    while(current is not None):
        next = current.next
        current.next = prev
        prev = current
        current = next
    self.head = prev