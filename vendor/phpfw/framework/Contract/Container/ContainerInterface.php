<?php
namespace Phpfw\Component\Contract\Container;

interface ContainerInterface
{
    /**
     * 
     * Menghapus semua instance dan alias
     * 
     * @param string $abstract
     * 
     * @return void
     * 
     */
	public function dropStaleInstance($abstract);

    /**
     * 
     * Menyatakan jika type yang diberikan bertipe singleton
     * 
     * @param string $abstract
     * 
     * @return boolean
     * 
     */
    public function isSingleton($abstract);

    /**
     * 
     * Menyatakan jika concrete yang diberikan bersifat buildable
     * 
     * @param mixed $concrete
     * @param string $abstract
     *  
     * @return boolean
     * 
     */
    public function isBuildable($concrete,  $abstract);

    /**
     * 
     * Menyatakan jika tipe abstract yang diberikan terpecahkan
     * 
     * @param string $abstract
     * 
     * @return boolean
     * 
     */
    public function resolved($abstract);

    /**
     * 
     * Menyatakan jika tipe abstract yang diberikan telah diikat kedalam container
     * 
     * @param string  $abstract
     * 
     * @return boolean
     * 
     */
    public function bound($abstract);

    /**
     * 
     * Mengambil tipe concrete untuk tipe abstract yang diberikan
     * 
     * @param  string  $abstract
     * 
     * @return mixed   $concrete
     * 
     */
    public function getConcrete($abstract);

	/**
	 * 
	 *  Meregistrasikan class dependencyResolver
	 *  @return void
	 * 
	 */
	public function registerDependencyManager();

    /**
     * 
     * Menginstantisasi instance concrete dari tipe yang diberikan
     *
     * @param  string  $concrete
     * @param  array   $parameters
     * 
     * @return mixed
     *
     * @throws BindingResolutionException
     * 
     */
    public function build($concrete,  $parameters = array());

    /**
     * 
     * Memecahkan type yang diberikan dari container
     *
     * @param  string  $abstract
     * @param  array   $parameters
     * 
     * @return mixed
     * 
     */
    public function make($abstract, $parameters = array());

    /**
     * 
     * Meregistraskan instance yang ada kedalam container
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * 
     * @return void
     * 
     */
    public function instance($abstract,  $instance);

    /**
     * 
     * Meregistrasikan binding kedalam container
     * 
     * @param string|array $abstract
     * @param Closure|string|null $concrete
     * @param boolean $singleton
     * 
     * @return void
     * 
     */
    public function bind($abstract,  $concrete,  $singleton = false);

    /**
     * 
     * Meregistrasikan tipe sebagai singleton object kedalam container
     * 
     * @param string $abstract
     * @param Closure|string|null $concrete
     * 
     * @return void
     * 
     */
    public function singleton($abstract,  $concrete = null);

    /**
     * 
     * Meregisrasikan binding jika tipe yang diberikan belum teregisrasi
     * 
     * @param string $abstract
     * @param Closure|string|null $concrete
     * 
     * @return void
     * 
     */
    public function bindIf($abstarct,  $concrete, $singleton = false);
}