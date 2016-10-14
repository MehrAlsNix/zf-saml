<?php
/**
 * ZF-SAML
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @copyright 2015 MehrAlsNix (http://www.mehralsnix.de)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://github.com/MehrAlsNix/zf-saml
 */

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Saml2SettingsFactory
 * @package MehrAlsNix\ZF\SAML\Factory
 */
class Saml2SettingsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     *
     * @return \OneLogin_Saml2_Settings
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OneLogin_Saml2_Error
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('Config');
        $config = isset($config['zf-saml']) ? $config['zf-saml'] : [];

        return new \OneLogin_Saml2_Settings($config);
    }
}
