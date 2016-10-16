<?php

namespace Mdobak\AutocompleteWidgetBundle\DataProvider\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Mdobak\AutocompleteWidgetBundle\DataProvider\AutocompleteItem;
use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DoctrineORMDataProvider implements DataProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $searchByColumn;
    /**
     * @var string
     */
    protected $idColumn;

    /**
     * @var null|string
     */
    protected $labelPropertyPath;

    /**
     * @var string
     */
    protected $idPropertyPath;

    /**
     * DoctrineORMDataProvider constructor.
     *
     * @param EntityManager $em
     * @param string        $class
     * @param string        $searchByColumn
     * @param string        $idColumn
     * @param string|null   $labelPropertyPath
     * @param string        $idPropertyPath
     */
    public function __construct(
        EntityManager $em,
        $class,
        $searchByColumn,
        $idColumn = 'id',
        $labelPropertyPath = null,
        $idPropertyPath = null
    ) {
        $this->em                   = $em;
        $this->class                = $class;
        $this->searchByColumn       = $searchByColumn;
        $this->idColumn             = $idColumn;
        $this->labelPropertyPath    = $labelPropertyPath;
        $this->idPropertyPath       = $idPropertyPath;

        if (null === $this->labelPropertyPath) {
            $this->labelPropertyPath = $this->searchByColumn;
        }

        if (null === $this->idPropertyPath) {
            $this->idPropertyPath = $this->idColumn;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findItem($key)
    {
        $result = $this->findItems([$key]);

        if (0 === count($result)) {
            return null;
        }

        return reset($result);
    }

    /**
     * {@inheritDoc}
     */
    public function findItems($keys)
    {
        $qb = $this
            ->em
            ->createQueryBuilder()
            ->select('e')
            ->from($this->class, 'e')
            ->where(sprintf('e.%s IN (:keys)', $this->idColumn))
            ->setParameter('keys', $keys)
        ;


        $results = [];
        foreach ($qb->getQuery()->getResult() as $entity) {
            $results[] = $this->wrapItem($entity);
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function findItemsForAutocomplete($searchString)
    {
        $qb = $this
            ->em
            ->createQueryBuilder()
            ->select('e')
            ->from($this->class, 'e')
            ->where(sprintf('e.%s LIKE :query', $this->searchByColumn))
            ->setParameter('query', $searchString . '%')
        ;

        $result = [];
        foreach ($qb->getQuery()->getResult() as $entity) {
            $result[] = $this->wrapItem($entity);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function wrapItem($entity)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $key = $accessor->getValue($entity, $this->idPropertyPath);

        if (null !== $this->labelPropertyPath) {
            $label = $accessor->getValue($entity, $this->labelPropertyPath);
        } else {
            $label = (string)$entity;
        }

        return new AutocompleteItem($key, $label, $entity);
    }
}
