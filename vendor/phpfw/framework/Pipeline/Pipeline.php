<?php
namespace Phpfw\Component\Pipeline;

use Closure;
use Phpfw\Component\Pipeline\Exception\PipelineException;
use Phpfw\Component\Contract\Pipeline\PipelineInterface as IPipeline;

class Pipeline implements IPipeline
{
    /**
     * @var mixed The input to send through the pipeline
     */
    private $input = null;
    
    /**
     * @var array The list of stages to send input through
     */
    private $stages = [];

    /**
     * @var string The method to call if the stages are not closures
     */
    private $methodToCall = null;
    
    /**
     * @var callable The callback to execute at the end
     */
    private $callback = null;
    
    /**
     * @inheritdoc
     */
	public function send($input): IPipeline
	{
		$this->input = $input;
		
		return $this;
	}

    /**
     * @inheritdoc
     */
    public function then(callable $callback) : IPipeline
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function through(array $stages, string $methodToCall = null) : IPipeline
    {
        $this->stages = $stages;
        $this->methodToCall = $methodToCall;

        return $this;
    }

    /**
     * 
     * Creates a callback for an individual stage
     *
     * @return Closure The callback
     * 
     * @throws PipelineException Thrown if there was a problem creating a stage
     * 
     */
    private function createStageCallback() : Closure
    {
        return function ($stages, $stage) {
            return function ($input) use ($stages, $stage) {
                if ($stage instanceof Closure) {
                    return $stage($input, $stages);
                } else {
                    if ($this->methodToCall === null) {
                        throw new PipelineException('Method must not be null');
                    }
                    
                    $ex = get_class($stage);
                    $ex.= "::{$this->methodToCall} does not exist";
                    
                    if (!method_exists($stage, $this->methodToCall)) {
                        throw new PipelineException($ex);
                    }

                    return $stage->{$this->methodToCall}($input, $stages);
                }
            };
        };
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return call_user_func(
            array_reduce(
                $this->stages, $this->createStageCallback(),
                function ($input) {
                    if ($this->callback === null) {
                        return $input;
                    }
                    return ($this->callback)($input);
                }), $this->input
        );
	}
}