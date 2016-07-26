<?php

	namespace KobiLab\Framework\Utilize;

	use KobiLab\Activities as A;

	class Activity
	{
		var $where = null;
		var $whichId = null;
		var $desc = null;

		public function create()
		{
			A::create([
				'where' => $this->where,
				'which_id' => $this->whichId,
				'desc' => $this->desc
			]);

			return true;
		}

		public function lot()
		{
			$this->where = 1;
			return $this;
		}

		public function whichId($id)
		{
			$this->whichId = $id;
			return $this;
		}

		public function desc($desc)
		{
			$this->desc = $desc;
			return $this;
		}
	}