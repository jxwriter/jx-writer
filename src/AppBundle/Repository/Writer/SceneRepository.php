<?php

namespace AppBundle\Repository\Writer;

/**
 * SceneRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SceneRepository extends \Doctrine\ORM\EntityRepository
{
	public $currentProject = null;

	public function getSceneListQueryBuilder($project=null){

		if (! $project) {
			$project = $this->currentProject;
		}

		$queryBuilder = $this->createQueryBuilder('e')
			->orderBy('e.id', 'ASC');

		if ($project) { 
			$queryBuilder
			->andWhere('e.project = :project')
			->setParameter('project', $project);
		}

        return $queryBuilder;

	}
}
