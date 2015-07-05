<?php namespace Flarum\Core\Search;

abstract class RegexGambit implements Gambit
{
    /**
     * The regex pattern to match the bit against.
     *
     * @var string
     */
    protected $pattern;

    /**
     * {@inheritdoc}
     */
    public function apply(Search $search, $bit)
    {
        if ($matches = $this->match($bit)) {
            list($negate) = array_splice($matches, 1, 1);

            $this->conditions($searcher, $matches, !! $negate);
        }

        return !! $matches;
    }

    /**
     * Match the bit against this gambit.
     *
     * @param string $bit
     * @return array
     */
    protected function match($bit)
    {
        if (preg_match('/^(-?)'.$this->pattern.'$/i', $bit, $matches)) {
            return $matches;
        }
    }

    /**
     * Apply conditions to the search, given that the gambit was matched.
     *
     * @param Search $search The search object.
     * @param array $matches An array of matches from the search bit.
     * @param bool $negate Whether or not the bit was negated, and thus whether
     *     or not the conditions should be negated.
     * @return mixed
     */
    abstract protected function conditions(Search $search, array $matches, $negate);
}