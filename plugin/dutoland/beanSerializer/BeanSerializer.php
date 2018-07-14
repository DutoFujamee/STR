<?php

namespace plugin\dutoland\beanSerializer;

use plugin\dutoland\beanSerializer\bean\BeanSerializerPack;
use ReflectionClass;

class BeanSerializer {

	public static function getBeanSerializerPackFromObject($object) {
		$reflexionClass = new ReflectionClass($object);

		$beanSerializerPack = new BeanSerializerPack($reflexionClass->getName());
		foreach ($reflexionClass->getProperties() as $property) {
			$property->setAccessible(true);
			$value = $property->getValue($object);
			if (is_object($value)) {
				$value = self::getBeanSerializerPackFromObject($value);
			} else if (is_array($value) && count($value) !== 0 && is_object(array_values($value)[0])) {
				$newValue = array();
				foreach ($value as $key => $childObject)
					$newValue[$key] = self::getBeanSerializerPackFromObject($childObject);
				$value = $newValue;
			}
			$beanSerializerPack->valueByPropertyName[$property->getName()] = $value;
		}

		return $beanSerializerPack;
	}

	public static function getObjectFromBeanSerializerPack(BeanSerializerPack $beanSerializerPack) {
		$object = new $beanSerializerPack->beanName();
		foreach ($beanSerializerPack->valueByPropertyName as $propertyName => $value) {
			if (property_exists($object, $propertyName)) {
				if ($value instanceof BeanSerializerPack) {
					$value = self::getObjectFromBeanSerializerPack($value);
				} else if (is_array($value) && count($value) !== 0 && array_values($value)[0] instanceof BeanSerializerPack) {
					$newValue = array();
					foreach ($value as $key => $childBeanSerializerPack)
						$newValue[$key] = self::getObjectFromBeanSerializerPack($childBeanSerializerPack);
					$value = $newValue;
				}
				$object->$propertyName = $value;
			}
		}

		return $object;
	}
}