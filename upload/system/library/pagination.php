<?php
class Pagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 8;
	public $url = '';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';

	public function render() {
		$total = $this->total;

		$page  = (($this->page < 1) ? 1 : $this->page);
		$limit = ((!(int)$this->limit) ? 10 : $this->limit);

		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);

		$output = '<ul class="pagination">';

		if ($page > 1) {
			$output .= '<li><a href="' . str_replace(array('?page={page}', '&page={page}', '?page=%7Bpage%7D', '&page=%7Bpage%7D'), '', $this->url) . '">' . $this->text_first . '</a></li>';

			if ($page - 1 === 1) {
				$output .= '<li><a href="' . str_replace(array('?page={page}', '&page={page}', '?page=%7Bpage%7D', '&page=%7Bpage%7D'), '', $this->url) . '">' . $this->text_prev . '</a></li>';
			} else {
				$output .= '<li><a href="' . str_replace(array('{page}', '%7Bpage%7D'), $page - 1, $this->url) . '">' . $this->text_prev . '</a></li>';
			}
		}

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= '<li class="active"><span>' . $i . '</span></li>';
				} else {
					if ($i === 1) {
						$output .= '<li><a href="' . str_replace(array('?page={page}', '&page={page}', '?page=%7Bpage%7D', '&page=%7Bpage%7D'), '', $this->url) . '">' . $i . '</a></li>';
					} else {
						$output .= '<li><a href="' . str_replace(array('{page}', '%7Bpage%7D'), $i, $this->url) . '">' . $i . '</a></li>';
					}
				}
			}
		}

		if ($page < $num_pages) {
			$output .= '<li><a href="' . str_replace(array('{page}', '%7Bpage%7D'), $page + 1, $this->url) . '">' . $this->text_next . '</a></li>';
			$output .= '<li><a href="' . str_replace(array('{page}', '%7Bpage%7D'), $num_pages, $this->url) . '">' . $this->text_last . '</a></li>';
		}

		$output .= '</ul>';

		if ($num_pages > 1) {
			return $output;
		} else {
			return '';
		}
	}
}