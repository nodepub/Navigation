<?php

namespace NodePub\Navigation;

/**
 * Stores a tree data structure
 */
class Tree implements \IteratorAggregate, \ArrayAccess
{
    /**
     * The parent node
     * @var NodePub\Navigation\Tree
     */
    protected $parent;

    /**
     * The value that represents the node
     * @var mixed
     */
    protected $value;

    /**
     * The node's attributes
     * @var array
     */
    protected $attributes;

    /**
     * The node's child nodes
     * @var array
     */
    protected $nodes;

    /**
     * @param mixed $value The value that represents the node
     */
    public function __construct($value = null)
    {
        $this->value  = $value;
        $this->parent = null;
        $this->nodes  = array();
        $this->attributes = array();
    }

    /**
     * Sets the node's value
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the node's value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the node's parent
     *
     * @param NodePub/Navigation/Tree
     */
    public function setParent(Tree $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns the node's parent
     *
     * @return NodePub/Navigation/Tree
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets a named node attribute
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns a named attribute of the node
     *
     * @return mixed|null
     */
    public function getAttribute($key, $default = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * Returns the previous child node in the parent's node array,
     * or null if this node is the first.
     *
     * @return NodePub/Navigation/Tree A reference to the previous node in the parent
     *                node collection or null if this is the first.
     */
    public function prevSibling()
    {
        if ($this->parent) {
            $index = $this->parent->indexOf($this);
    
            if ($index > 0) {
                return $this->parent->nodeAt($index - 1);
            }
        }
        
        return null;
    }

    /**
     * Returns the next child node in the parents node array,
     * or null if this node is the last.
     *
     * @return NodePub/Navigation/Tree A reference to the next node in the parent
     *                node collection or null if this is the last.
     */
    public function nextSibling()
    {
        if ($this->parent) {
          $myIndex = $this->parent->indexOf($this);
    
          if ($myIndex < ($this->parent->nodeCount() - 1)) {
              return $this->parent->nodeAt($myIndex + 1);
          }
        }
    
        return null;
    }

    /**
     * Returns true/false as to whether this node
     * has any child nodes or not.
     *
     * @return bool Any child nodes or not
     */
    public function hasChildren()
    {
        return $this->nodeCount() > 0;
    }
  
    /**
     * Returns the first node in this particular collection
     *
     * @return object The first node. NULL if no nodes.
     */
    public function firstChild()
    {
        return !empty($this->nodes) ? $this->nodes[0] : null;
    }

    /**
     * Returns the last node in this particular collection
     *
     * @return object The last node. NULL if no nodes.
     */
    public function lastChild()
    {
        return !empty($this->nodes) ? $this->nodes[$this->nodeCount() - 1] : null;
    }
  
    public function isFirstChild()
    {
        return $this->getParent() && $this->getParent()->firstChild() === $this;
    }
  
    public function isLastChild()
    {
        return $this->getParent() && $this->getParent()->lastChild() === $this;
    }
  
    /**
     * Returns the number of child nodes in this node/tree.
     * Optionally searches the tree and returns the cumulative count.
     * Works also as an implementation of the Countable::count() interface (PHP5.1+)
     * 
     * @param  bool    $search Search tree for nodecount too
     * @return integer         The number of nodes found
     */
    public function nodeCount($search = false)
    {
        if ($search) {
            $count = count($this->nodes);
              
            foreach ($this->nodes as $node) {
                $count += $node->nodeCount(true);
            }
      
            return $count;
        } else {
            return count($this->nodes);
        }
    }

    /**
     * Returns the depth in the tree of this node
     * This is a zero based indicator, so top level nodes
     * will have a depth of 0 (zero).
     *
     * @return integer The depth of the node
     */
    public function depth()
    {
        $depth = 0;
        $currLevel = $this;
    
        while ($currLevel->parent instanceof Tree) {
            ++$depth;
            $currLevel = $currLevel->parent;
        }
        
        return $depth;
    }

    /**
     * Returns true/false as to whether this node is a child
     * of the given node.
     *
     * @param  object $parent The suspected parent Tree or Tree_Node object
     * @return bool           Whether this node is a child of the suspected parent
     */
    public function isChildOf(Tree $parent)
    {
        return $this->parent === $parent;
    }

    /**
     * Adds a node to this node, or creates a new node
     *
     * @param  mixed  $node A Tree object or a new value
     * @return object A reference to the new node inside the tree
     */
    public function addNode($node)
    {
        $_class = get_class($this);
        
        if (is_string($node)) {
            $node = new $_class($node);
        }
        
        $node->setParent($this);
        $this->nodes[] = $node;
        
        return $node;
    }

    /**
     * Moves this node to a new parent. All child nodes will
     * be retained.
     *
     * @param object $newParent The new parent Tree_Node or Tree object
     */
    public function moveTo($newParent)
    {
        $newParent->addNode($this);
        $this->remove();
    }

    /**
     * Copies this node to a new parent. This copies the node
     * to the new parent node/tree and all its child nodes (ie
     * a deep copy). Technically, new nodes are created with copies
     * of the tag data, since this is for all intents and purposes
     * the only thing that needs copying.
     *
     * @param  object $newParent The new parent Tree_Node or Tree object
     * @return object            The new node
     */
    public function copyTo($newParent)
    {
        $newNode = $newParent->addNode($this->getValue());
        
        foreach ($this->nodes as $index => $node) {
            $node->copyTo($newNode);
        }
    
        return $newNode;
    }

    /**
     * Removes a node from the child nodes array at the
     * specified (zero based) index.
     *
     * @param  integer $index The index to remove
     * @return object         The node that was removed, or null
     *                        if this index did not exist
     */
    public function removeNodeAt($index)
    {
        $node = null;
        if (!empty($this->nodes[$index])) {
            $node = $this->nodes[$index]->remove();
            $this->nodes = array_values($this->nodes);
        }

        return $node;
    }

    /**
     * Removes a node from the child nodes array by using
     * reference comparison.
     *
     * @param  Tree_Node $node   The node to remove
     * @param  bool      $search Whether to search child nodes
     * @return bool              True/False
     */
    public function remove(array $node, $search = false)
    {
        foreach ($this->nodes as $index => $_node) {
            if ($_node === $node) {
                // Unset parent, tree
                $node->setParent(null);
        
                unset($this->nodes[$index]);
        
                $this->nodes = array_values($this->nodes);
                return true;
      
            } elseif ($search && $_node->hasChildren()) {
                $searchNodes[] = $_node;
            }
        }
  
      // Go through searching those nodes that have children
      if (!empty($searchNodes)) {
          foreach ($searchNodes as $_node) {
              if ($_node->nodes->remove($node, true)) {
                  return true;
              }
          }
      }
      
      return false;
    }

    /**
     * Returns the index in the nodes array at which
     * the given node resides. Used in the prev/next Sibling
     * methods.
     *
     * @param  object $node The node to return the index of
     * @return integer      The index of the node or null if
     *                      not found.
     */
    public function indexOf($node)
    {
        foreach ($this->nodes as $index => $child) {
            if ($node === $child) {
                return $index;
            }
        }
    
        return null;
    }

    /**
     * Returns node at given index.
     *
     * @param  integer $index Index of node to retrieve
     * @return NodePub/Navigation/Tree|null
     */
    public function nodeAt($index)
    {
        return isset($this->nodes[$index]) ? $this->nodes[$index] : null;
    }


    /**
     * Returns a flat list of the node collection. This array contains references
     * to the nodes.
     *
     * @return array Flat list of the nodes from top to bottom, left to right.
     */
    public function getFlatList($root=true)
    {
        $ret = array();

        // only append self the first time through
        if (true === $root) {
            $ret[]= $this;
        }
    
        foreach ($this->nodes as $key => $node) {
            $ret[] = $node;
              
            // Recurse
            if ($node->hasChildren()) {
                $ret = array_merge($ret, $node->getFlatList(false));
            }
        }
  
      return $ret;
    }

    /**
     * Traverses the node collection applying a function to each and every node.
     * The function name given (though this can be anything you can supply to
     * call_user_func(), not just a name) should take two arguments which are the
     * node object (Tree_Node class) and any extra data you pass via the $data argument
     * to traverse(). You can then access the nodes data by using
     * the getValue() method. The traversal goes from top to bottom, left to right
     * (ie same order as what you get from getFlatList()).
     *
     * ** The node is passed by reference to the function! **
     *
     * @param callback $callback The callback function to use
     * @param array    $data     Any data to pass on to the callback function.
     *                           Probably most useful as an array of "stuff".
     */
    public function traverse($callback, $data = null)
    {
        if (!is_callable($callback)) {
            return;
        }
        
        # run callback on current node
        call_user_func($callback, $this, $data);
        
        # run callback on child nodes
        foreach ($this->nodes as $node) {
            $node->traverse($callback, $data);
        }
    }

    /**
     * Searches the node collection for a node with a value matching
     * what you supply. This is a simple "value == your data" comparison, (=== if strict option is applied)
     * and more advanced comparisons can be made using the traverse() method.
     * This function returns an array of matching Tree objects.
     *
     * @param  mixed $data    Data to try to find and match
     * @param  bool  $strict  Whether to use === or simply == to compare
     * @return array          Array of resulting nodes matched.
     */
    public function search($data, $strict = false)
    {
        $results  = array();
        $nodeList = $this->getFlatList();
    
        foreach ($nodeList as $node) {
            $comparison = $strict ? ($node->getValue() === $data) : ($node->getValue() == $data);
                
            if ($comparison) {
                $results[] = $node;
            }
        }
        
        return $results;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     * 
     * @return object Iterator object for looping over this collections
     *                immediate nodes.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->nodes);
    }

    /**
     * Implementation of ArrayAccess:offsetSet()
     * 
     * @param mixed $key   Key to set value for
     * @param mixed $value Value to set
     */
    public function offsetSet($key, $node)
    {
        // TODO: check that $node is Tree instance

        $this->nodes[$key] = $node;
    }
  
    /**
     * Implementation of ArrayAccess:offsetGet()
     * 
     * @param  mixed $key Key to retrieve value of
     * @return mixed      Value of given key
     */
    public function offsetGet($key)
    {
        return $this->nodes[$key];
    }
  
    /**
     * Implementation of ArrayAccess:offsetUnset()
     * 
     * @param mixed $key Key to unset
     */
    public function offsetUnset($key)
    {
        unset($this->nodes[$key]);
    }
  
    /**
     * Implementation of ArrayAccess:offsetExists()
     * 
     * @param  mixed $key Key to check for
     * @return bool       Whether it's set or not
     */
    public function offsetExists($key)
    {
        return isset($this->nodes[$key]);
    }
}