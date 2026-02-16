<?php

namespace AMREU\GiltzaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Description of Configuration.
 *
 * @author ibilbao
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treebuilder = new TreeBuilder('giltza');
        $rootNode = $treebuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('provider')
                    ->children()
                        ->scalarNode('clientId')->info('Giltza client ID')->isRequired()->end()
                        ->scalarNode('clientSecret')->info('Giltza client secret')->isRequired()->end()
                        ->scalarNode('redirectUri')->isRequired()->defaultValue('amreu_giltza_login')->info('URL that redirects to giltza authentication')->end()
                        ->scalarNode('urlAuthorize')->defaultValue('https://eidas.izenpe.com/trustedx-authserver/oauth/izenpe')->info('Giltza authorization URL')->end()
                        ->scalarNode('urlAccessToken')->defaultValue('https://eidas.izenpe.com/trustedx-authserver/oauth/izenpe/token')->info('Giltza access token URL')->end()
                        ->scalarNode('urlResourceOwnerDetails')->defaultValue('https://eidas.izenpe.com/trustedx-resources/openid/v1/users/me')->info('Giltza resource owner details URL')->end()
                    ->end()
                ->end()
                ->arrayNode('controller')
                    ->children()
                        ->scalarNode('successUri')->defaultValue('amreu_giltza_success')->isRequired()->info('URL to go to after successful giltza login')->end()
                        ->scalarNode('response_type')->defaultValue('code')->end()
                        ->scalarNode('scope')->defaultValue('urn:izenpe:identity:global')->end()
                        ->scalarNode('prompt')->defaultValue('login')->end()
                        ->scalarNode('ui_locales')->defaultValue('eu')->end()
                        ->scalarNode('acr_values')->defaultValue('urn:safelayer:tws:policies:authentication:level:medium')->end()
                    ->end()
            ->end();
        return $treebuilder;
    }
}